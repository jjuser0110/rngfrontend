<?php

namespace App\Catalog\Database;

use App\Catalog\Contracts\HomestayCatalog;
use App\Models\Homestay;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/** Homestays staff add in rngcarrental (Homestays menu). */
class DatabaseHomestayCatalog extends DatabaseCatalog implements HomestayCatalog
{
    protected function model(): string
    {
        return Homestay::class;
    }

    protected function listColumns(): array
    {
        return ['amenities', 'house_rules'];
    }

    public function search(array $filters, int $perPage = 8): LengthAwarePaginator
    {
        $query = $this->whereKeyword($this->query(), $filters['q'] ?? null, ['name', 'area', 'address', 'type'])
            ->when($filters['area'] ?? null, fn ($q, $area) => $q->where('area', $area))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['max_price'] ?? null, fn ($q, $max) => $q->where('price_per_night', '<=', (float) $max))
            ->when($filters['bedrooms'] ?? null, fn ($q, $min) => $q->where('bedrooms', '>=', (int) $min))
            ->when($filters['guests'] ?? null, fn ($q, $min) => $q->where('guests', '>=', (int) $min));

        $query = match ($filters['sort'] ?? null) {
            'price_asc'  => $query->orderBy('price_per_night')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price_per_night')->orderBy('id'),
            'newest'     => $query->latest('id'),
            default      => $this->recommended($query),
        };

        return $this->paginate($query, $perPage);
    }

    /** Same area first, then the closest price. */
    public function similar(object $homestay, int $limit = 3): Collection
    {
        return $this->query()
            ->where('id', '!=', $homestay->id)
            ->orderByRaw('area = ? DESC', [$homestay->area])
            ->orderByRaw('ABS(price_per_night - ?)', [$homestay->price_per_night])
            ->limit($limit)
            ->get()
            ->map(fn ($other) => $this->toListing($other));
    }

    public function filterOptions(): array
    {
        return [
            'areas' => $this->distinctValues('area'),
            'types' => $this->distinctValues('type'),
        ];
    }
}
