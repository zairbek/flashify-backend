<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $email
 * @property string|null $confirmation_code
 * @property string|null $user_uuid
 * @property Carbon|null $send_at
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class UserEmail extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'confirmation_code',
        'send_at',
        'email_verified_at',
        'user_uuid',
    ];

    protected $casts = [
        'send_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];
}
