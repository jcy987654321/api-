<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdSlot extends Model
{
    protected $fillable = [
        'name',
        'identifier',
        'position',
        'description',
        'width',
        'height',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    public function activeAdvertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderByDesc('priority');
    }
}
