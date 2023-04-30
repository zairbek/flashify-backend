<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model
{
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'region_iso_code',
        'phone_number',
        'user_id',
        'confirmation_code',
        'send_at',
    ];

    protected $casts = [
        'send_at' => 'datetime'
    ];
}
