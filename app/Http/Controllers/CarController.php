<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Reservation;
use App\Models\VehicleModel;
use App\Rules\PhoneNumber;
use App\Services\BookingQuote;
use App\Support\WhatsApp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Customer booking flow: (home page search) -> fleet list -> (login) -> cart -> your details -> request sent.
 * The booking is kept in the session ("booking") until it is sent, then saved as a Pending
 * frontend reservation that staff review and confirm as a quotation in rngcarrental.
 */
class CarController extends Controller
{
    private const SESSION_KEY = 'booking';

    /** Website models, no availability check. Open to guests. */
    public function index(Request $request)
    {
        // Menu links ("Cars", "Rent Now") start a new booking; links inside the booking flow keep it
        if ($request->boolean('new')) {
            session()->forget(self::SESSION_KEY);

            return redirect()->route('cars.index', $request->except('new'));
        }

        $vehicleModels = VehicleModel::shownOnFrontend()
            ->whereNotNull('vehicle_class_id')
            ->with(['vehicleClass', 'media'])
            ->orderBy('name')
            ->get();

        if (in_array($request->sort_price, ['asc', 'desc'], true)) {
            $vehicleModels = $vehicleModels->sortBy(fn($vehicleModel) => $vehicleModel->rate('daily_rate'), SORT_REGULAR, $request->sort_price === 'desc')->values();
        }

        // After a home page search, show each car's rental price for those dates (extras are added in the cart)
        $schedule = $this->searchedSchedule(session(self::SESSION_KEY, []));
        $rentalQuotes = [];
        if ($schedule) {
            foreach ($vehicleModels as $vehicleModel) {
                $rentalQuotes[$vehicleModel->id] = BookingQuote::make($vehicleModel, array_merge($schedule, ['charges' => [], 'destinations' => []]));
            }
        }

        return view('cars.index', [
            'vehicleModels'   => $vehicleModels,
            'securityDeposit' => config('reservation.security_deposit'),
            'schedule'        => $schedule,
            'scheduleLength'  => $schedule ? BookingQuote::durationLabel(BookingQuote::duration(
                Carbon::parse($schedule['pickup_date'] . ' ' . $schedule['pickup_time']),
                Carbon::parse($schedule['return_date'] . ' ' . $schedule['return_time'])
            )) : null,
            'rentalQuotes'    => $rentalQuotes,
            'locations'       => $schedule ? BookingQuote::locations()->keyBy('id') : collect(),
        ]);
    }

    /**
     * Home page search (Car/Bike or Bus): keep the dates and locations for the cart, then show the fleet.
     * Open to guests; the draft survives login, so the cart opens already filled in.
     *
     * Locations are typed (or picked from suggestions): a branch name becomes that location, anything
     * else is an "Other address" (location 0). An empty drop-off means the same place as pick-up.
     */
    public function search(Request $request)
    {
        $data = $request->all();
        if (blank($data['return_place'] ?? null)) {
            $data['return_place'] = $data['pickup_place'] ?? null;
        }
        $locations = BookingQuote::locations();
        foreach (['pickup', 'return'] as $side) {
            $place = trim((string) ($data[$side . '_place'] ?? ''));
            $location = $place === '' ? null : $locations->first(fn($location) => strcasecmp($location->name, $place) === 0);
            $data[$side . '_location_id'] = $place === '' ? null : ($location->id ?? 0);
            $data[$side . '_address'] = $place === '' || $location ? null : $place;
        }

        $rules = array_intersect_key($this->scheduleRules(), array_flip([
            'pickup_location_id', 'pickup_address', 'return_location_id', 'return_address',
            'pickup_date', 'pickup_time', 'return_date', 'return_time',
        ]));
        $rules['vehicle_type'] = 'nullable|in:car_bike,bus';

        $validator = Validator::make($data, $rules, $this->scheduleMessages() + [
            'pickup_location_id.required' => 'Please enter the pick-up location.',
        ]);
        $this->checkScheduleTimes($validator, $data);

        if ($validator->fails()) {
            return redirect()->to(route('welcome') . '#vehicle-search')->withErrors($validator)->withInput();
        }

        // Keep any car and extras already chosen; only the schedule changes
        session([self::SESSION_KEY => array_merge(session(self::SESSION_KEY, []), $validator->validated(), ['searched' => true])]);

        return redirect()->route('cars.index');
    }

    /** "Select Vehicle": guests are sent to login / register first, then come back here. */
    public function select(VehicleModel $vehicleModel)
    {
        if (!$this->isBookable($vehicleModel)) {
            return redirect()->route('cars.index')->withErrors('This vehicle is no longer available to book online. Please choose another.');
        }

        // Keep any schedule and extras already entered; only the car changes
        $draft = session(self::SESSION_KEY, []);
        $draft['vehicle_model_id'] = $vehicleModel->id;
        session([self::SESSION_KEY => $draft]);

        return redirect()->route('cars.cart');
    }

    public function cart()
    {
        $draft = session(self::SESSION_KEY, []);
        $vehicleModel = $this->draftVehicleModel($draft);
        if (!$vehicleModel) {
            return redirect()->route('cars.index')->withErrors('Please choose a vehicle first.');
        }

        $locations = BookingQuote::locations();
        $draft = array_merge(BookingQuote::defaultSchedule($locations), $draft);

        return view('cars.cart', [
            'draft'        => $draft,
            'vehicleModel' => $vehicleModel,
            'locations'    => $locations,
            'chargeTypes'  => BookingQuote::chargeTypes(),
            'destinations' => BookingQuote::destinations(),
            'quote'        => BookingQuote::make($vehicleModel, $draft),
            'timeSlots'    => BookingQuote::timeSlots(),
        ]);
    }

    /** Live summary for the cart (priced on the server, so the browser never does the maths). */
    public function quote(Request $request)
    {
        $vehicleModel = $this->draftVehicleModel(session(self::SESSION_KEY, []));
        if (!$vehicleModel) {
            return response()->json(['message' => 'Please choose a vehicle first.'], 422);
        }

        // The address doesn't change the price, so the summary doesn't wait for it (Proceed still requires it)
        $rules = $this->scheduleRules();
        $rules['pickup_address'] = $rules['return_address'] = 'nullable|string|max:500';

        $validator = Validator::make($request->all(), $rules);
        $this->checkScheduleTimes($validator, $request->all());

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        $quote = BookingQuote::make($vehicleModel, $validator->validated());

        return response()->json([
            'summary' => view('cars.partials.summary', compact('quote', 'vehicleModel'))->render(),
            'total'   => 'RM ' . number_format($quote['total'], 2),
        ]);
    }

    public function updateCart(Request $request)
    {
        $draft = session(self::SESSION_KEY, []);
        $vehicleModel = $this->draftVehicleModel($draft);
        if (!$vehicleModel) {
            return redirect()->route('cars.index')->withErrors('Please choose a vehicle first.');
        }

        $validator = Validator::make($request->all(), $this->scheduleRules(), $this->scheduleMessages());
        $this->checkScheduleTimes($validator, $request->all());
        $validated = $validator->validate();

        $validated['charges'] = array_filter($validated['charges'] ?? []);
        $validated['destinations'] = array_values($validated['destinations'] ?? []);
        // Addresses only apply to "other address" locations (id 0), like rngcarrental step 1
        foreach (['pickup', 'return'] as $side) {
            if ((int) $validated[$side . '_location_id'] !== 0) {
                $validated[$side . '_address'] = null;
            }
        }

        session([self::SESSION_KEY => array_merge($draft, $validated, ['cart_done' => true])]);

        return redirect()->route('cars.fillinfo');
    }

    public function details()
    {
        $draft = session(self::SESSION_KEY, []);
        $vehicleModel = $this->draftVehicleModel($draft);
        if (!$vehicleModel || empty($draft['cart_done'])) {
            return redirect()->route($vehicleModel ? 'cars.cart' : 'cars.index');
        }

        $customer = $this->customer();
        if (!$customer) {
            return redirect()->route('myaccount.index')->withErrors('Your account has no customer profile. Please contact us to make a booking.');
        }

        return view('cars.fillinfo', [
            'isConfirmed'  => false,
            'customer'     => $customer,
            'draft'        => $draft,
            'vehicleModel' => $vehicleModel,
            'quote'        => BookingQuote::make($vehicleModel, $draft),
            'locations'    => BookingQuote::locations()->keyBy('id'),
        ]);
    }

    public function submit(Request $request)
    {
        // One send at a time per customer, so a double click or a resent form can't create two bookings
        $lock = Cache::lock('booking-submit:' . Auth::id(), 30);
        if (!$lock->get()) {
            return back()->withErrors('Your booking request is already being sent. Please wait a moment.');
        }

        try {
            return $this->sendBookingRequest($request);
        } finally {
            $lock->release();
        }
    }

    private function sendBookingRequest(Request $request)
    {
        $draft = session(self::SESSION_KEY, []);

        // Already sent (e.g. the browser resent the form): show that request instead of an error
        if (empty($draft) && session('booking_sent')) {
            return redirect()->route('cars.confirmed', session('booking_sent'));
        }

        $vehicleModel = $this->draftVehicleModel($draft);
        if (!$vehicleModel || empty($draft['cart_done'])) {
            return redirect()->route($vehicleModel ? 'cars.cart' : 'cars.index')
                ->withErrors('Your booking has expired or the vehicle is no longer available. Please check your cart.');
        }

        $customer = $this->customer();
        if (!$customer) {
            return redirect()->route('myaccount.index')->withErrors('Your account has no customer profile. Please contact us to make a booking.');
        }

        $validated = $request->validate([
            'phone_number'   => ['required', 'string', 'max:20', new PhoneNumber],
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        // The schedule may have gone stale while the customer filled in details (e.g. pickup time passed)
        $scheduleCheck = Validator::make($draft, $this->scheduleRules(), $this->scheduleMessages());
        $this->checkScheduleTimes($scheduleCheck, $draft);
        if ($scheduleCheck->fails()) {
            return redirect()->route('cars.cart')->withErrors($scheduleCheck);
        }

        $quote = BookingQuote::make($vehicleModel, $draft);

        $reservation = DB::transaction(function () use ($customer, $draft, $vehicleModel, $quote, $validated) {
            $customer->update(['phone_number' => PhoneNumber::normalize($validated['phone_number'])]);

            $reservation = Reservation::create([
                'customer_id'        => $customer->id,
                'pickup_date'        => $draft['pickup_date'],
                'pickup_time'        => $draft['pickup_time'],
                'return_date'        => $draft['return_date'],
                'return_time'        => $draft['return_time'],
                'pickup_location_id' => $draft['pickup_location_id'],
                'return_location_id' => $draft['return_location_id'],
                'pickup_address'     => $draft['pickup_address'] ?? null,
                'return_address'     => $draft['return_address'] ?? null,
                'airport'            => $quote['airport'] ? 1 : 0,
                // The class follows the model so staff can assign a car by class in rngcarrental
                'vehicle_class_id'   => $vehicleModel->vehicle_class_id,
                'vehicle_model_id'   => $vehicleModel->id,
                'security_deposit'   => $quote['security_deposit'],
                'status'             => 'Pending',
                'source'             => 'frontend',
                'created_by'         => Auth::id(),
            ]);

            $reservation->rateDetails()->create(array_merge(
                $quote['duration'],
                $quote['rates'],
                [
                    'airport'     => $quote['airport'] ? 1 : 0,
                    'pickup_date' => $draft['pickup_date'],
                    'pickup_time' => $draft['pickup_time'],
                    'return_date' => $draft['return_date'],
                    'return_time' => $draft['return_time'],
                ]
            ));

            if ($quote['charges']) {
                $reservation->chargeDetails()->createMany($quote['charges']);
            }

            // Shown to staff in the reservation's comments
            $note = 'Booked on the website.';
            if (!empty($validated['customer_notes'])) {
                $note .= "\nCustomer note: " . $validated['customer_notes'];
            }
            $reservation->comments()->create(['comment' => $note, 'created_by' => Auth::id()]);

            $reservation->unsetRelation('rateDetails');
            $reservation->unsetRelation('chargeDetails');
            $reservation->recalculateTotalPrice();

            return $reservation;
        });

        session()->forget(self::SESSION_KEY);
        session(['booking_sent' => $reservation->id]);

        return redirect()->route('cars.confirmed', $reservation);
    }

    /** "Request sent" page for one of the customer's own web bookings. */
    public function confirmed(Reservation $reservation)
    {
        $customer = $this->customer();
        abort_unless($customer && $reservation->customer_id == $customer->id && $reservation->isFrontend(), 404);

        $reservation->load(['vehicleModel.media', 'pickupLocation', 'returnLocation', 'chargeDetails', 'rateDetails']);

        return view('cars.fillinfo', [
            'isConfirmed'    => true,
            'reservation'    => $reservation,
            'customer'       => $customer,
            'whatsappNumber' => WhatsApp::number(),
        ]);
    }

    // ---------------------------------------------------------------------

    private function isBookable(?VehicleModel $vehicleModel): bool
    {
        return $vehicleModel
            && $vehicleModel->is_active
            && $vehicleModel->show_on_frontend
            && $vehicleModel->vehicle_class_id;
    }

    private function draftVehicleModel(array $draft): ?VehicleModel
    {
        $vehicleModel = !empty($draft['vehicle_model_id'])
            ? VehicleModel::with(['vehicleClass', 'media'])->find($draft['vehicle_model_id'])
            : null;

        return $this->isBookable($vehicleModel) ? $vehicleModel : null;
    }

    private function customer(): ?Customer
    {
        $customerId = Auth::user()->customer_id ?? null;

        return $customerId ? Customer::find($customerId) : null;
    }

    private function scheduleRules(): array
    {
        $location = function ($attribute, $value, $fail) {
            if ((int) $value !== 0 && !Location::where('is_active', 1)->whereKey($value)->exists()) {
                $fail('Please choose a valid location.');
            }
        };

        return [
            'pickup_location_id' => ['required', 'integer', $location],
            'pickup_address'     => 'nullable|required_if:pickup_location_id,0|string|max:500',
            'return_location_id' => ['required', 'integer', $location],
            'return_address'     => 'nullable|required_if:return_location_id,0|string|max:500',
            'pickup_date'        => 'required|date_format:Y-m-d|after_or_equal:today',
            'pickup_time'        => 'required|date_format:H:i',
            'return_date'        => 'required|date_format:Y-m-d|after_or_equal:pickup_date',
            'return_time'        => 'required|date_format:H:i',
            'charges'            => 'nullable|array',
            'charges.*'          => 'integer|min:0|max:99',
            'destinations'       => 'nullable|array',
            'destinations.*'     => 'integer|exists:outstation_destinations,id',
        ];
    }

    private function scheduleMessages(): array
    {
        return [
            'pickup_address.required_if' => 'Please enter the pick-up address.',
            'return_address.required_if' => 'Please enter the drop-off address.',
            'pickup_date.after_or_equal' => 'The pick-up date cannot be in the past.',
            'return_date.after_or_equal' => 'The drop-off date must be on or after the pick-up date.',
            'pickup_time.required'       => 'Please choose the pick-up time.',
            'return_time.required'       => 'Please choose the drop-off time.',
        ];
    }

    /** Pick-up must be in the future and drop-off after pick-up. */
    private function checkScheduleTimes($validator, array $data): void
    {
        $validator->after(function ($validator) use ($data) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $pickup = Carbon::parse($data['pickup_date'] . ' ' . $data['pickup_time']);
            $return = Carbon::parse($data['return_date'] . ' ' . $data['return_time']);

            if ($pickup->lte(now())) {
                $validator->errors()->add('pickup_time', 'The pick-up time has already passed. Please choose a later time.');
            } elseif ($return->lte($pickup)) {
                $validator->errors()->add('return_time', 'The drop-off time must be after the pick-up time.');
            }
        });
    }

    /** The schedule from the home page search, if it is still usable (pick-up not yet passed). */
    private function searchedSchedule(array $draft): ?array
    {
        if (empty($draft['searched'])) {
            return null;
        }

        $validator = Validator::make($draft, $this->scheduleRules());
        $this->checkScheduleTimes($validator, $draft);

        return $validator->fails() ? null : $draft;
    }
}
