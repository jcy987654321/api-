<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RemoteToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'token',
        'description',
        'permissions',
        'rate_limit_per_hour',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'rate_limit_per_hour' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function apiCalls(): HasMany
    {
        return $this->hasMany(ApiCall::class);
    }
}