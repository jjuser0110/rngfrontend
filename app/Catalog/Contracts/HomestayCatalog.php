<?php

namespace App\Catalog\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Where the website reads homestays from. Today: SampleHomestayCatalog (resources/data/homestays.php).
 * When rngcarrental manages homestays, add an Eloquent version and change the binding in AppServiceProvider;
 * the controller and views stay the same as long as each homestay offers these fields:
 *
 *   slug, name, type, area, address, map_query, price_per_night (RM), guests, bedrooms, beds,
 *   bathrooms, size_sqft, check_in, check_out, min_nights, summary, description (paragraphs),
 *   amenities (names), house_rules, images (URLs, first = cover),
 *   photo_credits (optional, per image: [caption, author, licence, licence URL, source URL]), listed_at (Y-m-d),
 *   is_sample (true shows a "Sample listing" label; real listings leave it out)
 */
interface HomestayCatalog
{
    /** Filters: q, area, type, max_price, bedrooms (min), guests (min), sort (recommended|price_asc|price_desc|newest). */
    public function search(array $filters, int $perPage = 8): LengthAwarePaginator;

    public function findBySlug(string $slug): ?object;

    public function similar(object $homestay, int $limit = 3): Collection;

    /** Choices for the filter bar: ['areas' => [...], 'types' => [...]]. */
    public function filterOptions(): array;
}
