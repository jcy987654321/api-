<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    const POSITION_HEADER = 'header';
    const POSITION_SIDEBAR = 'sidebar';
    const POSITION_FOOTER = 'footer';
    const POSITION_CONTENT = 'content';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'name',
        'image_url',
        'link_url',
        'description',
        'position',
        'status',
        'starts_at',
        'ends_at',
        'impressions',
        'clicks',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'impressions' => 'integer',
            'clicks' => 'integer',
        ];
    }
}