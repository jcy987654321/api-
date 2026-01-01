<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateLimit extends Model
{
    protected $fillable = [
        'api_key_id',
        'count',
        'window_start_at',
        'reset_at',
    ];

    protected $casts = [
        'count' => 'integer',
        'window_start_at' => 'datetime',
        'reset_at' => 'datetime',
    ];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->reset_at);
    }

    public function incrementCount(): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        $this->increment('count');
        return true;
    }

    public function resetWindow(int $windowSeconds): void
    {
        $this->update([
            'count' => 1,
            'window_start_at' => now(),
            'reset_at' => now()->addSeconds($windowSeconds),
        ]);
    }

    public function remaining(int $limit): int
    {
        return max(0, $limit - $this->count);
    }
}