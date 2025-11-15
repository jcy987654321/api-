<?php

namespace App\Models;

use App\Services\FeedbackEncryptionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_thread_id',
        'sender_type',
        'content_encrypted',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime_type',
        'attachment_size',
        'is_redacted',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'attachment_size' => 'integer',
        'is_redacted' => 'boolean',
    ];

    protected $hidden = [
        'content_encrypted',
    ];

    protected $appends = [
        'content',
        'attachment_url',
        'formatted_size',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            if (empty($message->sent_at)) {
                $message->sent_at = now();
            }
        });
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(FeedbackThread::class, 'feedback_thread_id');
    }

    public function getContentAttribute(): string
    {
        if ($this->is_redacted) {
            return '[Content redacted by administrator]';
        }

        return app(FeedbackEncryptionService::class)->decryptContent($this->content_encrypted);
    }

    public function setContentAttribute(string $value): void
    {
        $this->attributes['content_encrypted'] = app(FeedbackEncryptionService::class)->encryptContent($value);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (empty($this->attachment_path)) {
            return null;
        }

        return route('feedback.attachment.download', ['message' => $this->id]);
    }

    public function getFormattedSizeAttribute(): ?string
    {
        if (empty($this->attachment_size)) {
            return null;
        }

        $bytes = $this->attachment_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function redact(): void
    {
        $this->update(['is_redacted' => true]);
    }

    public function unredact(): void
    {
        $this->update(['is_redacted' => false]);
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function isImage(): bool
    {
        if (!$this->hasAttachment()) {
            return false;
        }

        return in_array($this->attachment_mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ]);
    }

    public function isPdf(): bool
    {
        if (!$this->hasAttachment()) {
            return false;
        }

        return $this->attachment_mime_type === 'application/pdf';
    }

    public function isTextDocument(): bool
    {
        if (!$this->hasAttachment()) {
            return false;
        }

        return in_array($this->attachment_mime_type, [
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function scopeVisitor($query)
    {
        return $query->where('sender_type', 'visitor');
    }

    public function scopeAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }

    public function scopeUnredacted($query)
    {
        return $query->where('is_redacted', false);
    }

    public function scopeWithAttachment($query)
    {
        return $query->whereNotNull('attachment_path');
    }

    public function getAttachmentPath(): ?string
    {
        if (empty($this->attachment_path)) {
            return null;
        }

        return storage_path('app/' . $this->attachment_path);
    }

    public function deleteAttachment(): bool
    {
        if (!$this->hasAttachment()) {
            return true;
        }

        $path = $this->getAttachmentPath();
        
        if (file_exists($path)) {
            unlink($path);
        }

        $this->update([
            'attachment_path' => null,
            'attachment_original_name' => null,
            'attachment_mime_type' => null,
            'attachment_size' => null,
        ]);

        return true;
    }
}