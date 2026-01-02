<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notify_feedback_reply',
        'notify_email',
        'notify_system',
    ];

    protected $casts = [
        'notify_feedback_reply' => 'boolean',
        'notify_email' => 'boolean',
        'notify_system' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaults(): array
    {
        return [
            'notify_feedback_reply' => true,
            'notify_email' => true,
            'notify_system' => true,
        ];
    }

    public static function getOrCreateForUser(int $userId): self
    {
        $preference = self::where('user_id', $userId)->first();

        if (!$preference) {
            $preference = self::create(array_merge(
                self::getDefaults(),
                ['user_id' => $userId]
            ));
        }

        return $preference;
    }
}
