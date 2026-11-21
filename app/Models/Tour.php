<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A tour staff manage in rngcarrental (Tours menu). The website only reads it.
 * Photos: media collection "images", first = cover.
 */
class Tour extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $casts = [
        'highlights'       => 'array',
        'itinerary'        => 'array',
        'includes'         => 'array',
        'excludes'         => 'array',
        'what_to_bring'    => 'array',
        'good_to_know'     => 'array',
        'show_on_frontend' => 'boolean',
        'price_per_person' => 'float',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function scopeShownOnFrontend(Builder $query): Builder
    {
        return $query->where('show_on_frontend', true);
    }
}
