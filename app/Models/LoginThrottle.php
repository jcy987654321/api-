<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginThrottle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'failed_attempts',
        'first_attempt_at',
        'last_attempt_at',
        'locked_until',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'first_attempt_at' => 'datetime',
            'last_attempt_at' => 'datetime',
            'locked_until' => 'datetime',
            'is_locked' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getOrCreateForIp(string $ip, ?int $userId = null, ?string $email = null): self
    {
        return self::updateOrCreate(
            ['ip_address' => $ip, 'user_id' => $userId],
            ['email' => $email]
        );
    }

    public function isLocked(): bool
    {
        return $this->is_locked && $this->locked_until && $this->locked_until->isFuture();
    }

    public function recordFailure(): void
    {
        $this->increment('failed_attempts');
        $this->update(['last_attempt_at' => now()]);
    }

    public function resetAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'first_attempt_at' => null,
            'is_locked' => false,
            'locked_until' => null,
        ]);
    }
}
