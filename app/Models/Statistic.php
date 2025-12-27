<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'type',
        'value',
        'date',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'integer',
        'metadata' => 'array',
    ];
}
