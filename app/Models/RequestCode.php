<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $uuid
 * @property string $account_uuid
 * @property string|null $email
 * @property array|null $phone
 * @property string $code
 * @property DateTime $sendAt
 */
class RequestCode extends Model
{
    use HasFactory;
    protected $table = 'accounts-request_codes';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'account_uuid',
        'email',
        'phone',
        'code',
        'sendAt',
    ];

    protected $casts = [
        'sendAt' => 'datetime',
        'phone' => 'array',
    ];
}
