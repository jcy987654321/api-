<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'name',
        'description',
        'version',
        'status',
        'config',
    ];

    protected $casts = [
        'status' => 'boolean',
        'config' => 'array',
    ];
}
