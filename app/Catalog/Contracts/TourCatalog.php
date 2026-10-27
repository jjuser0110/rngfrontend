<?php

namespace App\Catalog\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Where the website reads tours from. Today: SampleTourCatalog (resources/data/tours.php).
 * When rngcarrental manages tours, add an Eloquent version and change the binding in AppServiceProvider;
 * the controller and views stay the same as long as each tour offers these fields:
 *
 *   slug, name, category, duration_type (half_day|full_day|multi_day), duration_label, start_time,
 *   price_per_person (RM), min_pax, pickup_point, summary, description (paragraphs), highlights,
 *   itinerary (list of [time, title, detail]), includes, excludes, what_to_bring, images (URLs, first = cover),
 *   photo_credits (optional, per image: [caption, author, licence, licence URL, source URL]),
 *   good_to_know (optional notes such as seasons and closures)
 */
interface TourCatalog
{
    public const DURATIONS = [
        'half_day'  => 'Half day',
        'full_day'  => 'Full day',
        'multi_day' => 'Multi-day',
    ];

    /** Filters: q, category, duration (a DURATIONS key), sort (recommended|price_asc|price_desc). */
    public function search(array $filters, int $perPage = 9): LengthAwarePaginator;

    public function findBySlug(string $slug): ?object;

    public function similar(object $tour, int $limit = 3): Collection;

    /** Choices for the filter bar: ['categories' => [...]]. */
    public function filterOptions(): array;
}
