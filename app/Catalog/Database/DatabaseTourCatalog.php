<?php

namespace App\Catalog\Database;

use App\Catalog\Contracts\TourCatalog;
use App\Models\Tour;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/** Tours staff add in rngcarrental (Tours menu). */
class DatabaseTourCatalog extends DatabaseCatalog implements TourCatalog
{
    protected function model(): string
    {
        return Tour::class;
    }

    protected function listColumns(): array
    {
        return ['highlights', 'itinerary', 'includes', 'excludes', 'what_to_bring', 'good_to_know'];
    }

    public function search(array $filters, int $perPage = 9): LengthAwarePaginator
    {
        $query = $this->whereKeyword($this->query(), $filters['q'] ?? null, ['name', 'category', 'summary'])
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['duration'] ?? null, fn ($q, $duration) => $q->where('duration_type', $duration));

        $query = match ($filters['sort'] ?? null) {
            'price_asc'  => $query->orderBy('price_per_person')->orderBy('id'),
            'price_desc' => $query->orderByDesc('price_per_person')->orderBy('id'),
            default      => $this->recommended($query),
        };

        return $this->paginate($query, $perPage);
    }

    /** Same category first, then the usual order. */
    public function similar(object $tour, int $limit = 3): Collection
    {
        return $this->recommended(
            $this->query()->where('id', '!=', $tour->id)->orderByRaw('category = ? DESC', [$tour->category])
        )
            ->limit($limit)
            ->get()
            ->map(fn ($other) => $this->toListing($other));
    }

    public function filterOptions(): array
    {
        return [
            'categories' => $this->distinctValues('category'),
        ];
    }
}
