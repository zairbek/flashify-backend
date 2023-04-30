<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPhone extends Model
{
    use HasFactory;

    protected $fillable = [
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
