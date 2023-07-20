<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property null|string $externalId
 * @property null|array $translates
 * @property null|integer $parent_id
 * @property Carbon $created_at
 * @property null|Carbon $updated_at
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
}
