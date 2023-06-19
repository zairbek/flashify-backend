<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Account extends Model
{
    use Notifiable;
    use HasApiTokens;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'login',
        'first_name',
        'last_name',
        'region_iso_code',
        'phone_number',
        'email',
        'confirmation_code',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
    ];
}
