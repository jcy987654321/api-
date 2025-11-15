<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackThread extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';
    const STATUS_SPAM = 'spam';

    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
        'priority',
        'contact_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'contact_encrypted' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackMessages(): HasMany
    {
        return $this->hasMany(FeedbackMessage::class);
    }

    public function friendLinkApplications(): HasMany
    {
        return $this->hasMany(FriendLinkApplication::class);
    }
}