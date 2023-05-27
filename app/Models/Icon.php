<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property string $uuid
 * @property string $name
 * @property Media $file
 */
class Icon extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const MEDIA_COLLECTION = 'file';

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'name'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION)
            ->acceptsMimeTypes(['image/svg+xml'])
            ->singleFile();
    }

    public function file(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMedia(self::MEDIA_COLLECTION)
        );
    }

    public function addIcon(UploadedFile $file): void
    {
        $this->addMedia($file)->toMediaCollection(self::MEDIA_COLLECTION);
    }

    public function toIconFile(): IconFile
    {
        $file = $this->file;

        return new IconFile(
            originalName: $file->file_name,
            filename: $file->name,
            mimeType: $file->mime_type,
            dirPath: $file->getPath(),
            filePath: $file->getUrl(),
            isUploaded: true
        );
    }
}
