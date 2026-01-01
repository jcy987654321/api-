<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKeyLog extends Model
{
    protected $fillable = [
        'api_key_id',
        'method',
        'endpoint',
        'status_code',
        'response_time',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'response_time' => 'integer',
        'status_code' => 'integer',
    ];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}