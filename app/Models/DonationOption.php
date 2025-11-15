<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationOption extends Model
{
    protected $fillable = [
        'name',
        'description',
        'payment_method',
        'payment_link',
        'qr_code_image',
        'icon_image',
        'instructions',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
