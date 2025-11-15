<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Backup extends Model
{
    use HasFactory;

    const STATUS_CREATING = 'creating';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_DELETED = 'deleted';

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'type',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function siteSetting(): BelongsTo
    {
        return $this->belongsTo(SiteSetting::class);
    }
}