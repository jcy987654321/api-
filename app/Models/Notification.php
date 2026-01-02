<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'related_id',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public const TYPE_FEEDBACK_REPLY = 'feedback_reply';
    public const TYPE_SYSTEM_MESSAGE = 'system_message';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function isRead(): bool
    {
        return $this->is_read === true;
    }

    public function isUnread(): bool
    {
        return $this->is_read === false;
    }

    public function getRelatedModelAttribute()
    {
        if ($this->type === self::TYPE_FEEDBACK_REPLY && $this->related_id) {
            return Feedback::find($this->related_id);
        }
        return null;
    }
}
