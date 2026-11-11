<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Files uploaded before the form they belong to is saved (register, My Account licence).
 * Each upload is tied to the visitor's session: only they can add to it, delete from it or use it.
 */
class TemporaryUpload extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    private const SESSION_KEY = 'temporary_uploads';

    protected $fillable = [
        'uuid',
    ];

    /** This visitor's upload with that uuid, or null if it isn't theirs (or doesn't exist). */
    public static function ofSession(?string $uuid): ?self
    {
        if (!$uuid || !in_array($uuid, session(self::SESSION_KEY, []), true)) {
            return null;
        }

        return static::where('uuid', $uuid)->first();
    }

    /** Starts a new upload for this visitor. */
    public static function startForSession(): self
    {
        $upload = static::create(['uuid' => (string) Str::uuid()]);
        session()->push(self::SESSION_KEY, $upload->uuid);

        return $upload;
    }
}
