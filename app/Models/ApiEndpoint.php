<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiEndpoint extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DEPRECATED = 'deprecated';
    const STATUS_DRAFT = 'draft';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'method',
        'path',
        'description',
        'response_format',
        'sort_order',
        'status',
        'hits_count',
        'last_called_at',
    ];

    protected function casts(): array
    {
        return [
            'last_called_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ApiCategory::class);
    }

    public function apiParameters(): HasMany
    {
        return $this->hasMany(ApiParameter::class);
    }

    public function apiExamples(): HasMany
    {
        return $this->hasMany(ApiExample::class);
    }

    public function apiCalls(): HasMany
    {
        return $this->hasMany(ApiCall::class);
    }

    public function apiDailyStats(): HasMany
    {
        return $this->hasMany(ApiDailyStat::class);
    }
}