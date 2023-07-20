<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property integer $id
 * @property string $name
 * @property null|string $externalId
 * @property null|array $translates
 * @property null|integer $parent_id
 * @property Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|self $parent
 * @property Collection $children
 */
class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'externalId',
        'translates',
        'parent_id',
    ];

    protected $casts = [
        'translates' => 'array'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class);
    }
}
