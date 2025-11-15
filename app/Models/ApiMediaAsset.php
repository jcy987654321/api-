<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiMediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'file_path',
        'mime_type',
        'file_size',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ApiCategory::class);
    }
}