<?php

namespace App\Catalog\Database;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;

/**
 * Listings staff manage in rngcarrental, read from the shared database. Each record is handed to the views
 * as the same kind of object the sample catalogs give (see App\Catalog\Contracts), so the pages don't change.
 */
abstract class DatabaseCatalog
{
    /** Shown when a listing has no photos yet. */
    public const PLACEHOLDER_IMAGE = 'images/background/3.jpg';

    /** The Eloquent model class. */
    abstract protected function model(): string;

    /** Columns that are lists (JSON arrays). */
    abstract protected function listColumns(): array;

    /** Listings shown on the website, with their photos. */
    protected function query(): Builder
    {
        return $this->model()::query()->shownOnFrontend()->with('media');
    }

    /** "Recommended": the order staff set (List Order), then newest first. */
    protected function recommended(Builder $query): Builder
    {
        return $query->orderByRaw('position IS NULL')->orderBy('position')->latest('id');
    }

    protected function whereKeyword(Builder $query, ?string $keyword, array $columns): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        $like = '%' . addcslashes(trim($keyword), '\\%_') . '%';

        return $query->where(function (Builder $q) use ($columns, $like) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', $like);
            }
        });
    }

    public function findBySlug(string $slug): ?object
    {
        $listing = $this->query()->where('slug', $slug)->first();

        return $listing ? $this->toListing($listing) : null;
    }

    protected function paginate(Builder $query, int $perPage): LengthAwarePaginator
    {
        return $query->paginate($perPage)->withQueryString()->through(fn (Model $listing) => $this->toListing($listing));
    }

    protected function distinctValues(string $column): array
    {
        return $this->model()::query()->shownOnFrontend()->distinct()->orderBy($column)->pluck($column)->all();
    }

    /** The record as the views expect it: photo URLs, description paragraphs, lists never null. */
    protected function toListing(Model $listing): Fluent
    {
        $data = $listing->attributesToArray();

        $images = $listing->getMedia('images')->map->getUrl()->all();
        $data['images'] = $images ?: [asset(self::PLACEHOLDER_IMAGE)];
        $data['description'] = array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $listing->description))));
        foreach ($this->listColumns() as $column) {
            $data[$column] = $data[$column] ?? [];
        }
        $data['photo_credits'] = [];
        $data['is_sample'] = false;
        $data['listed_at'] = optional($listing->created_at)->toDateString();

        return new Fluent($data);
    }
}
