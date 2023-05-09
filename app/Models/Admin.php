<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'sex',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
    ];
}
