<?php

namespace App\Catalog\Sample;

use App\Catalog\Contracts\TourCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SampleTourCatalog extends SampleCatalog implements TourCatalog
{
    protected function dataFile(): string
    {
        return 'tours.php';
    }

    public function search(array $filters, int $perPage = 9): LengthAwarePaginator
    {
        $tours = $this->items()
            ->filter(fn($tour) => self::matchesKeyword($tour, $filters['q'] ?? null, ['name', 'category', 'summary']))
            ->when($filters['category'] ?? null, fn($items, $category) => $items->where('category', $category))
            ->when($filters['duration'] ?? null, fn($items, $duration) => $items->where('duration_type', $duration));

        $tours = match ($filters['sort'] ?? null) {
            'price_asc'  => $tours->sortBy('price_per_person'),
            'price_desc' => $tours->sortByDesc('price_per_person'),
            default      => $tours,
        };

        return $this->paginate($tours->values(), $perPage);
    }

    /** Same category first, then the rest in list order. */
    public function similar(object $tour, int $limit = 3): Collection
    {
        return $this->items()
            ->reject(fn($other) => $other->slug === $tour->slug)
            ->sortBy(fn($other) => $other->category === $tour->category ? 0 : 1)
            ->take($limit)
            ->values();
    }

    public function filterOptions(): array
    {
        return [
            'categories' => $this->items()->pluck('category')->unique()->sort()->values()->all(),
        ];
    }
}
