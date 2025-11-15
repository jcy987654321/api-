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
}