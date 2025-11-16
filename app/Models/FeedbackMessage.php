<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackMessage extends Model
{
    use HasFactory;

    const TYPE_USER = 'user';
    const TYPE_STAFF = 'staff';
    const TYPE_SYSTEM = 'system';

    protected $fillable = [
        'thread_id',
        'user_id',
        'content',
        'type',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(FeedbackThread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}