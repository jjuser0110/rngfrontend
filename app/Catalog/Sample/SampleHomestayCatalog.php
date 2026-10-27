<?php

namespace App\Catalog\Sample;

use App\Catalog\Contracts\HomestayCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SampleHomestayCatalog extends SampleCatalog implements HomestayCatalog
{
    protected function dataFile(): string
    {
        return 'homestays.php';
    }

    public function search(array $filters, int $perPage = 8): LengthAwarePaginator
    {
        $homestays = $this->items()
            ->filter(fn($homestay) => self::matchesKeyword($homestay, $filters['q'] ?? null, ['name', 'area', 'address', 'type']))
            ->when($filters['area'] ?? null, fn($items, $area) => $items->where('area', $area))
            ->when($filters['type'] ?? null, fn($items, $type) => $items->where('type', $type))
            ->when($filters['max_price'] ?? null, fn($items, $max) => $items->where('price_per_night', '<=', (float) $max))
            ->when($filters['bedrooms'] ?? null, fn($items, $min) => $items->where('bedrooms', '>=', (int) $min))
            ->when($filters['guests'] ?? null, fn($items, $min) => $items->where('guests', '>=', (int) $min));

        $homestays = match ($filters['sort'] ?? null) {
            'price_asc'  => $homestays->sortBy('price_per_night'),
            'price_desc' => $homestays->sortByDesc('price_per_night'),
            'newest'     => $homestays->sortByDesc('listed_at'),
            default      => $homestays,
        };

        return $this->paginate($homestays->values(), $perPage);
    }

    /** Same area first, then the closest price. */
    public function similar(object $homestay, int $limit = 3): Collection
    {
        return $this->items()
            ->reject(fn($other) => $other->slug === $homestay->slug)
            ->sortBy(fn($other) => [$other->area === $homestay->area ? 0 : 1, abs($other->price_per_night - $homestay->price_per_night)])
            ->take($limit)
            ->values();
    }

    public function filterOptions(): array
    {
        return [
            'areas' => $this->items()->pluck('area')->unique()->sort()->values()->all(),
            'types' => $this->items()->pluck('type')->unique()->sort()->values()->all(),
        ];
    }
}
