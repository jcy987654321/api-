<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    use HasFactory;

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILURE = 'failure';
    const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
        'country',
        'country_name',
        'city',
        'latitude',
        'longitude',
        'device_type',
        'browser_name',
        'browser_version',
        'os_name',
        'os_version',
        'is_mobile',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}