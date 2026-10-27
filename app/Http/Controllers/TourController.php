<?php

namespace App\Http\Controllers;

use App\Catalog\Contracts\TourCatalog;
use App\Support\WhatsApp;
use Illuminate\Http\Request;

/** Tour list and detail pages. Enquiries are by WhatsApp; no login needed. */
class TourController extends Controller
{
    public function __construct(private TourCatalog $tours)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'        => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'duration' => 'nullable|in:' . implode(',', array_keys(TourCatalog::DURATIONS)),
            'sort'     => 'nullable|in:recommended,price_asc,price_desc',
        ]);

        return view('tours.index', [
            'tours'     => $this->tours->search($filters),
            'filters'   => $filters,
            'options'   => $this->tours->filterOptions(),
            'durations' => TourCatalog::DURATIONS,
        ]);
    }

    public function show(string $slug)
    {
        $tour = $this->tours->findBySlug($slug) ?? abort(404);

        return view('tours.show', [
            'tour'         => $tour,
            'similar'      => $this->tours->similar($tour),
            'whatsappLink' => WhatsApp::link("Hi, I'd like to ask about the tour \"{$tour->name}\".\n" . route('tours.show', $tour->slug)),
        ]);
    }
}
