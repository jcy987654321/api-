<?php

namespace App\Models;

use Database\Factories\ApiKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'key',
        'secret',
        'rate_limit',
        'rate_window',
        'status',
        'last_used_at',
    ];

    protected $casts = [
        'rate_limit' => 'integer',
        'rate_window' => 'integer',
        'last_used_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->key)) {
                $model->key = 'API-' . Str::random(32);
            }
        });
    }

    public static function generateSecret(): string
    {
        return Str::random(64);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ApiKeyLog::class);
    }

    public function rateLimit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RateLimit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function recordUsage(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public function regenerateSecret(): self
    {
        $newSecret = self::generateSecret();
        $this->update([
            'secret' => bcrypt($newSecret),
        ]);
        
        // Store plaintext secret temporarily for display
        $this->plaintextSecret = $newSecret;
        $this->plaintextKey = $this->key;
        
        return $this;
    }

    // Store plaintext values for initial display
    public ?string $plaintextSecret = null;
    public ?string $plaintextKey = null;
}