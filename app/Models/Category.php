<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property string $uuid
 * @property string $slug
 * @property string $name
 * @property ?string $description
 * @property ?string $icon_uuid
 * @property ?string $parent_id
 * @property boolean $active
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property ?Category $parent
 * @property Collection<Category> $children
 * @property ?Icon $icon
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
        'parent_uuid',
        'active',
        'icon_uuid'
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

    public function icon(): HasOne
    {
        return $this->hasOne(Icon::class, 'uuid', 'icon_uuid');
    }
}
