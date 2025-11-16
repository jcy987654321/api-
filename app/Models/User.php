<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_PENDING = 'pending';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'last_login_at',
        'last_login_ip',
        'bio',
        'avatar',
        'is_admin',
        'otp_secret',
        'otp_enabled',
        'failed_login_attempts',
        'account_locked_until',
        'is_account_locked',
        'password_changed_at',
        'password_history',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_admin' => 'boolean',
            'otp_enabled' => 'boolean',
            'is_account_locked' => 'boolean',
            'account_locked_until' => 'datetime',
            'password_history' => 'array',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function userSessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class);
    }

    public function adminSessions(): HasMany
    {
        return $this->hasMany(AdminSession::class);
    }

    public function loginThrottle()
    {
        return $this->hasOne(LoginThrottle::class);
    }

    public function feedbackThreads(): HasMany
    {
        return $this->hasMany(FeedbackThread::class);
    }

    public function feedbackMessages(): HasMany
    {
        return $this->hasMany(FeedbackMessage::class);
    }

    public function friendLinkApplications(): HasMany
    {
        return $this->hasMany(FriendLinkApplication::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function apiCalls(): HasMany
    {
        return $this->hasMany(ApiCall::class);
    }

    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class)
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin || $this->hasRole('admin');
    }

    public function isAccountLocked(): bool
    {
        if (!$this->is_account_locked) {
            return false;
        }

        if ($this->account_locked_until && $this->account_locked_until->isPast()) {
            $this->update(['is_account_locked' => false]);
            return false;
        }

        return true;
    }

    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'is_account_locked' => true,
            'account_locked_until' => now()->addMinutes($minutes),
        ]);
    }

    public function unlockAccount(): void
    {
        $this->update([
            'is_account_locked' => false,
            'account_locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
    }

    public function resetFailedAttempts(): void
    {
        $this->update(['failed_login_attempts' => 0]);
    }
}