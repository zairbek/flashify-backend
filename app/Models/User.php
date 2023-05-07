<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property string $uuid
 * @property string $login
 * @property string|null $password
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $sex
 * @property string $active
 * @property UserPhone|null $phone
 * @property UserEmail|null $email
 * @property
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'login',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'sex',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
    ];

    public function phone(): HasOne
    {
        return $this->hasOne(UserPhone::class, 'user_id', 'uuid');
    }

    public function email(): HasOne
    {
        return $this->hasOne(UserEmail::class);
    }
}
