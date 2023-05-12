<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property string $uuid
 * @property string $slug
 * @property string $name
 * @property ?string $description
 * @property ?string $icon
 * @property ?string $parent_id
 * @property boolean $active
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property ?Category $parent
 * @property Collection<Category> $children
 */
class Category extends Model
{

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'slug',
        'name',
        'description',
        'icon',
        'parent_uuid',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean'
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
