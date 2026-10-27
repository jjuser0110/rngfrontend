<?php

namespace App\Catalog\Sample;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

/** Hardcoded listings read from a PHP file in resources/data, until they are managed in rngcarrental. */
abstract class SampleCatalog
{
    private ?Collection $items = null;

    /** File name in resources/data, e.g. "homestays.php". */
    abstract protected function dataFile(): string;

    /** Every listing as an object (->name, ->images ...), with image paths turned into URLs. */
    protected function items(): Collection
    {
        return $this->items ??= collect(require resource_path('data/' . $this->dataFile()))
            ->map(function (array $item) {
                $item['images'] = array_map(fn($path) => asset($path), $item['images'] ?? []);

                return new Fluent($item);
            });
    }

    public function findBySlug(string $slug): ?object
    {
        return $this->items()->firstWhere('slug', $slug);
    }

    /** Same paginator type as Eloquent's paginate(), keeping the filters in the page links. */
    protected function paginate(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    protected static function matchesKeyword(object $item, ?string $keyword, array $fields): bool
    {
        if (blank($keyword)) {
            return true;
        }

        $haystack = implode(' ', array_map(fn($field) => $item->{$field}, $fields));

        return str_contains(mb_strtolower($haystack), mb_strtolower(trim($keyword)));
    }
}
