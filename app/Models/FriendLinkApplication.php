<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FriendLinkApplication extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'user_id',
        'feedback_thread_id',
        'site_name',
        'site_url',
        'description',
        'contact_encrypted',
        'status',
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

    public function feedbackThread(): BelongsTo
    {
        return $this->belongsTo(FeedbackThread::class);
    }
}