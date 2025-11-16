<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint_id',
        'user_id',
        'visitor_session_id',
        'remote_token_id',
        'ip_address',
        'user_agent',
        'request_headers',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',
        'response_time_ms',
    ];

    protected function casts(): array
    {
        return [
            'response_status' => 'integer',
            'response_time_ms' => 'integer',
        ];
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(ApiEndpoint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitorSession(): BelongsTo
    {
        return $this->belongsTo(VisitorSession::class);
    }

    public function remoteToken(): BelongsTo
    {
        return $this->belongsTo(RemoteToken::class);
    }
}