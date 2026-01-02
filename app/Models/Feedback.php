<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tracking_code',
        'name',
        'email',
        'type',
        'title',
        'content',
        'status',
        'priority',
        'assigned_to',
        'attachments',
        'ip_address',
        'user_agent',
        'resolved_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'resolved_at' => 'datetime',
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_REPLIED = 'replied';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    public const TYPE_BUG = 'bug';
    public const TYPE_FEATURE = 'feature';
    public const TYPE_SUGGESTION = 'suggestion';
    public const TYPE_OTHER = 'other';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'Anonymous',
        ]);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FeedbackReply::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function markAsResolved(): bool
    {
        return $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'FB-' . strtoupper(bin2hex(random_bytes(8)));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NEW => 'bg-info',
            self::STATUS_REVIEWING => 'bg-warning',
            self::STATUS_REPLIED => 'bg-primary',
            self::STATUS_RESOLVED => 'bg-success',
            self::STATUS_CLOSED => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'bg-success',
            self::PRIORITY_MEDIUM => 'bg-warning',
            self::PRIORITY_HIGH => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_BUG => 'Bug Report',
            self::TYPE_FEATURE => 'Feature Request',
            self::TYPE_SUGGESTION => 'Suggestion',
            self::TYPE_OTHER => 'Other',
            default => 'Other',
        };
    }
}
