<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiExample extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint_id',
        'title',
        'request_example',
        'response_example',
        'description',
        'sort_order',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(ApiEndpoint::class);
    }
}