<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiParameter extends Model
{
    use HasFactory;

    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';

    const LOCATION_QUERY = 'query';
    const LOCATION_PATH = 'path';
    const LOCATION_HEADER = 'header';
    const LOCATION_COOKIE = 'cookie';
    const LOCATION_BODY = 'body';

    protected $fillable = [
        'endpoint_id',
        'name',
        'type',
        'location',
        'description',
        'required',
        'default_value',
        'validation_rules',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
        ];
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(ApiEndpoint::class);
    }
}