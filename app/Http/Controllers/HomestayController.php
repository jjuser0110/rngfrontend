<?php

namespace App\Http\Controllers;

use App\Catalog\Contracts\HomestayCatalog;
use App\Support\WhatsApp;
use Illuminate\Http\Request;

/** Homestay list and detail pages. Booking is by WhatsApp; no login needed. */
class HomestayController extends Controller
{
    public function __construct(private HomestayCatalog $homestays)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'         => 'nullable|string|max:100',
            'area'      => 'nullable|string|max:100',
            'type'      => 'nullable|string|max:100',
            'max_price' => 'nullable|integer|min:0',
            'bedrooms'  => 'nullable|integer|min:1|max:10',
            'guests'    => 'nullable|integer|min:1|max:30',
            'sort'      => 'nullable|in:recommended,price_asc,price_desc,newest',
        ]);

        return view('homestays.index', [
            'homestays' => $this->homestays->search($filters),
            'filters'   => $filters,
            'options'   => $this->homestays->filterOptions(),
        ]);
    }

    public function show(string $slug)
    {
        $homestay = $this->homestays->findBySlug($slug) ?? abort(404);

        return view('homestays.show', [
            'homestay'     => $homestay,
            'similar'      => $this->homestays->similar($homestay),
            // Without JavaScript the button still opens a chat about this homestay
            'whatsappLink' => WhatsApp::link("Hi, I'm interested in booking the homestay \"{$homestay->name}\".\n" . route('homestays.show', $homestay->slug)),
        ]);
    }
}
