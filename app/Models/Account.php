<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property string $uuid
 * @property string $login
 * @property string $first_name
 * @property string $last_name
 * @property string|null $region_iso_code
 * @property string|null $phone_number
 * @property string|null $email
 * @property array|null $confirmation_code
 * @property string|null $password
 * @property string $status
 */
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
        'confirmation_code' => 'array'
    ];
}
