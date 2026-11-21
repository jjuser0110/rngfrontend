<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * A homestay staff manage in rngcarrental (Homestays menu). The website only reads it.
 * Photos: media collection "images", first = cover.
 */
class Homestay extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $casts = [
        'amenities'        => 'array',
        'house_rules'      => 'array',
        'show_on_frontend' => 'boolean',
        'price_per_night'  => 'float',
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
