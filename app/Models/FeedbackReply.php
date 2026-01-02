<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_id',
        'user_id',
        'name',
        'content',
        'is_admin',
        'attachments',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'attachments' => 'array',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Anonymous',
        ]);
    }

    public function isFromAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isFromUser(): bool
    {
        return $this->is_admin === false;
    }
}
