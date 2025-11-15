<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiDailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint_id',
        'date',
        'total_calls',
        'successful_calls',
        'failed_calls',
        'unique_visitors',
        'total_response_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_calls' => 'integer',
            'successful_calls' => 'integer',
            'failed_calls' => 'integer',
            'unique_visitors' => 'integer',
            'total_response_time' => 'integer',
        ];
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(ApiEndpoint::class);
    }
}