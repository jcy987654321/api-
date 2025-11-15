<?php

namespace App\Models;

use App\Services\FeedbackEncryptionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FeedbackThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'visitor_name',
        'visitor_email_encrypted',
        'subject_encrypted',
        'status',
        'admin_notes_encrypted',
        'visitor_ip',
        'visitor_user_agent',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    protected $hidden = [
        'visitor_email_encrypted',
        'subject_encrypted',
        'admin_notes_encrypted',
    ];

    protected $appends = [
        'visitor_email',
        'subject',
        'admin_notes',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            if (empty($thread->reference_id)) {
                $thread->reference_id = 'FB-' . strtoupper(Str::random(8));
            }
            $thread->last_activity_at = now();
        });

        static::updating(function ($thread) {
            $thread->last_activity_at = now();
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(FeedbackMessage::class)->orderBy('sent_at');
    }

    public function visitorMessages(): HasMany
    {
        return $this->messages()->where('sender_type', 'visitor');
    }

    public function adminMessages(): HasMany
    {
        return $this->messages()->where('sender_type', 'admin');
    }

    public function getVisitorEmailAttribute(): string
    {
        return app(FeedbackEncryptionService::class)->decryptEmail($this->visitor_email_encrypted);
    }

    public function setVisitorEmailAttribute(string $value): void
    {
        $this->attributes['visitor_email_encrypted'] = app(FeedbackEncryptionService::class)->encryptEmail($value);
    }

    public function getSubjectAttribute(): string
    {
        return app(FeedbackEncryptionService::class)->decryptSubject($this->subject_encrypted);
    }

    public function setSubjectAttribute(string $value): void
    {
        $this->attributes['subject_encrypted'] = app(FeedbackEncryptionService::class)->encryptSubject($value);
    }

    public function getAdminNotesAttribute(): ?string
    {
        if (empty($this->admin_notes_encrypted)) {
            return null;
        }
        return app(FeedbackEncryptionService::class)->decryptAdminNotes($this->admin_notes_encrypted);
    }

    public function setAdminNotesAttribute(?string $value): void
    {
        if (empty($value)) {
            $this->attributes['admin_notes_encrypted'] = null;
        } else {
            $this->attributes['admin_notes_encrypted'] = app(FeedbackEncryptionService::class)->encryptAdminNotes($value);
        }
    }

    public function getUnreadCountAttribute(): int
    {
        return $this->messages()
            ->where('sender_type', 'visitor')
            ->where('created_at', '>', $this->last_activity_at->subMinutes(5))
            ->count();
    }

    public function getLatestMessageAttribute(): ?FeedbackMessage
    {
        return $this->messages()->latest('sent_at')->first();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function markAsClosed(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function markAsOpen(): void
    {
        $this->update(['status' => 'open']);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    public function generateVisitorToken(): string
    {
        return encrypt([
            'thread_id' => $this->id,
            'reference_id' => $this->reference_id,
            'email' => $this->visitor_email,
            'expires_at' => now()->addDays(30),
        ]);
    }

    public static function findByVisitorToken(string $token): ?self
    {
        try {
            $data = decrypt($token);
            
            if (now()->gt($data['expires_at'])) {
                return null;
            }

            $thread = self::where('id', $data['thread_id'])
                ->where('reference_id', $data['reference_id'])
                ->first();

            if ($thread && strtolower($thread->visitor_email) === strtolower($data['email'])) {
                return $thread;
            }
        } catch (\Exception $e) {
            // Invalid token
        }

        return null;
    }
}