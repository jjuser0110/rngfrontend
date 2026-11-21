<?php

namespace Tests\Feature;

use App\Models\Homestay;
use App\Models\Tour;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Homestay and tour pages, reading what staff add in rngcarrental (Homestays / Tours menu).
 *
 * Runs against the database shared with rngcarrental: every test is rolled back (DatabaseTransactions).
 * Never switch this to RefreshDatabase.
 */
class ListingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** As rngcarrental saves it: lists as JSON arrays, optional fields left blank unless given. */
    private function homestay(array $extra = []): Homestay
    {
        return Homestay::forceCreate($extra + [
            'slug' => 'zz-homestay-' . uniqid(), 'name' => 'ZZ Homestay ' . uniqid(), 'type' => 'Condominium', 'area' => 'ZZ Area',
            'price_per_night' => 180, 'guests' => 4, 'bedrooms' => 2, 'min_nights' => 1,
            'summary' => 'A test homestay.', 'show_on_frontend' => true,
        ]);
    }

    private function tour(array $extra = []): Tour
    {
        return Tour::forceCreate($extra + [
            'slug' => 'zz-tour-' . uniqid(), 'name' => 'ZZ Tour ' . uniqid(), 'category' => 'ZZ Wildlife',
            'duration_type' => 'half_day', 'duration_label' => 'Half day (4 hours)', 'price_per_person' => 120, 'min_pax' => 2,
            'summary' => 'A test tour.', 'show_on_frontend' => true,
        ]);
    }

    // ---------------------------------------------------------------- homestays

    public function test_a_full_homestay_shows_everything_staff_entered(): void
    {
        $homestay = $this->homestay([
            'address' => '12 Jalan ZZ, Kuching', 'beds' => 3, 'bathrooms' => 2, 'size_sqft' => 950,
            'check_in' => '15:00', 'check_out' => '12:00', 'min_nights' => 2,
            'description' => "First paragraph.\n\nSecond paragraph.",
            'amenities' => ['Wi-Fi', 'Baby cot'], 'house_rules' => ['No smoking indoors'],
        ]);
        $homestay->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('images');

        $this->get(route('homestays.index'))->assertOk()->assertSee($homestay->name)->assertSee('950 sq ft')->assertSee('check-in 15:00');

        $page = $this->get(route('homestays.show', $homestay->slug))->assertOk()
            ->assertSee($homestay->name)->assertSee('12 Jalan ZZ, Kuching')
            ->assertSee('First paragraph.')->assertSee('Second paragraph.')
            ->assertSee('Wi-Fi')->assertSee('Baby cot')->assertSee('No smoking indoors')
            ->assertSee('From 15:00')->assertSee('By 12:00')->assertSee('Minimum 2 nights')
            ->assertDontSee('Sample listing')
            ->getContent();
        $this->assertStringContainsString('cover.jpg', $page, 'the staff photo is shown');
    }

    public function test_a_homestay_with_only_the_required_fields_still_shows(): void
    {
        $homestay = $this->homestay(); // no photos, address, beds, bathrooms, size, check-in/out, description, lists

        $this->get(route('homestays.index'))->assertOk()->assertSee($homestay->name)->assertDontSee('sq ft');
        $this->get(route('homestays.show', $homestay->slug))->assertOk()
            ->assertSee($homestay->name)
            ->assertSee('ZZ Area, Kuching')                 // area instead of the address
            ->assertSee('images/background/3.jpg', false)  // plain picture until photos are added
            ->assertDontSee('About this homestay')
            ->assertDontSee('Amenities</h2>', false)
            ->assertDontSee('Check-in</div>', false);
    }

    public function test_hidden_and_deleted_homestays_are_not_on_the_website(): void
    {
        $hidden = $this->homestay(['show_on_frontend' => false]);
        $deleted = $this->homestay();
        $deleted->delete();

        $this->get(route('homestays.index'))->assertOk()->assertDontSee($hidden->name)->assertDontSee($deleted->name);
        $this->get(route('homestays.show', $hidden->slug))->assertNotFound();
        $this->get(route('homestays.show', $deleted->slug))->assertNotFound();
    }

    public function test_homestay_filters_and_sort(): void
    {
        $cheap = $this->homestay(['area' => 'ZZ North', 'price_per_night' => 90, 'bedrooms' => 1, 'guests' => 2]);
        $big = $this->homestay(['area' => 'ZZ South', 'type' => 'ZZ Villa', 'price_per_night' => 900, 'bedrooms' => 5, 'guests' => 12]);

        $this->get(route('homestays.index', ['area' => 'ZZ North']))->assertSee($cheap->name)->assertDontSee($big->name);
        $this->get(route('homestays.index', ['type' => 'ZZ Villa']))->assertSee($big->name)->assertDontSee($cheap->name);
        $this->get(route('homestays.index', ['bedrooms' => 5, 'q' => 'ZZ']))->assertSee($big->name)->assertDontSee($cheap->name);
        $this->get(route('homestays.index', ['q' => $cheap->name]))->assertSee($cheap->name)->assertDontSee($big->name);

        // The filter bar offers the areas and types staff used
        $this->get(route('homestays.index'))->assertSee('ZZ North')->assertSee('ZZ Villa');

        $html = $this->get(route('homestays.index', ['q' => 'ZZ Homestay', 'sort' => 'price_desc']))->getContent();
        $this->assertLessThan(strpos($html, $cheap->name), strpos($html, $big->name), 'dearest first');
    }

    public function test_list_order_set_by_staff_comes_first(): void
    {
        $second = $this->homestay(['position' => 2]);
        $first = $this->homestay(['position' => 1]);

        $html = $this->get(route('homestays.index', ['q' => 'ZZ Homestay']))->getContent();
        $this->assertLessThan(strpos($html, $second->name), strpos($html, $first->name));
    }

    public function test_similar_homestays_prefer_the_same_area(): void
    {
        $homestay = $this->homestay(['area' => 'ZZ Same']);
        $sameArea = $this->homestay(['area' => 'ZZ Same', 'price_per_night' => 999]);

        $this->get(route('homestays.show', $homestay->slug))->assertSee($sameArea->name);
    }

    public function test_empty_and_no_match_messages(): void
    {
        $this->get(route('homestays.index', ['q' => 'nothing-like-this-' . uniqid()]))->assertSee('No homestays match your search');

        Homestay::query()->update(['show_on_frontend' => false]); // nothing listed (rolled back)
        $this->get(route('homestays.index'))->assertOk()->assertSee('No homestays listed yet')->assertSee('WhatsApp us');
    }

    // ---------------------------------------------------------------- tours

    public function test_a_full_tour_shows_everything_staff_entered(): void
    {
        $tour = $this->tour([
            'start_time' => '07:30', 'pickup_point' => 'Your hotel in Kuching',
            'description' => "Overview paragraph.\n\nMore about it.",
            'highlights' => ['Orangutan feeding'],
            'itinerary' => [['07:30', 'Pick-up', 'Driver meets you.'], ['', 'Feeding time', '']],
            'includes' => ['Transport'], 'excludes' => ['Tips'], 'what_to_bring' => ['Insect repellent'],
            'good_to_know' => ['Fewer sightings Nov - Mar'],
        ]);

        $this->get(route('tours.index'))->assertOk()->assertSee($tour->name)->assertSee('Starts 07:30');
        $this->get(route('tours.show', $tour->slug))->assertOk()
            ->assertSee('Overview paragraph.')->assertSee('Orangutan feeding')
            ->assertSee('Pick-up')->assertSee('Driver meets you.')->assertSee('Feeding time')
            ->assertSee('Transport')->assertSee('Tips')->assertSee('Insect repellent')->assertSee('Fewer sightings Nov - Mar')
            ->assertSee('Your hotel in Kuching')->assertSee('Minimum 2 people');
    }

    public function test_a_tour_with_only_the_required_fields_still_shows(): void
    {
        $tour = $this->tour();

        $this->get(route('tours.index'))->assertOk()->assertSee($tour->name);
        $this->get(route('tours.show', $tour->slug))->assertOk()
            ->assertSee($tour->name)
            ->assertDontSee('Itinerary</h2>', false)
            ->assertDontSee("What's included", false)
            ->assertDontSee('Good to know');
    }

    public function test_tour_filters_hidden_and_deleted(): void
    {
        $half = $this->tour(['category' => 'ZZ Nature']);
        $multi = $this->tour(['duration_type' => 'multi_day', 'duration_label' => '2 days 1 night']);
        $hidden = $this->tour(['show_on_frontend' => false]);

        $this->get(route('tours.index', ['category' => 'ZZ Nature']))->assertSee($half->name)->assertDontSee($multi->name);
        $this->get(route('tours.index', ['duration' => 'multi_day']))->assertSee($multi->name)->assertDontSee($half->name);
        $this->get(route('tours.index'))->assertDontSee($hidden->name);
        $this->get(route('tours.show', $hidden->slug))->assertNotFound();

        $half->delete();
        $this->get(route('tours.show', $half->slug))->assertNotFound();
    }

    public function test_tours_empty_message(): void
    {
        Tour::query()->update(['show_on_frontend' => false]);
        $this->get(route('tours.index'))->assertOk()->assertSee('No tours listed yet');
    }

    public function test_prices_keep_their_sen(): void
    {
        $withSen = $this->homestay(['price_per_night' => 149.50, 'area' => 'ZZ Price']);
        $this->homestay(['price_per_night' => 1280, 'area' => 'ZZ Price']);
        $tour = $this->tour(['price_per_person' => 89.90]);

        $this->get(route('homestays.index', ['area' => 'ZZ Price']))
            ->assertSee('RM 149.50')->assertSee('RM 1,280')->assertDontSee('RM 150 ')->assertDontSee('RM 1,280.00');
        $this->get(route('homestays.show', $withSen->slug))->assertSee('RM 149.50')->assertSee('RM 1,280'); // similar listing too
        $this->get(route('tours.index'))->assertSee('RM 89.90');
        $this->get(route('tours.show', $tour->slug))->assertSee('RM 89.90');
    }

    public function test_sample_listings_are_no_longer_shown(): void
    {
        $this->get('/homestays/riverine-resort-3-bedroom-condo')->assertNotFound();
        $this->get('/tours/semenggoh-orangutan-morning')->assertNotFound();
    }
}
