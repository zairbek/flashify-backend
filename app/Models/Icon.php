<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property string $uuid
 * @property string $name
 * @property string $file
 */
class Icon extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'name'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')
            ->acceptsMimeTypes(['image/svg+xml'])
            ->singleFile();
    }

    public function file(): Attribute
    {
        $this->load('media');
        return Attribute::make(
            get: fn () => $this->getFirstMediaUrl('file')
        );
    }
}
