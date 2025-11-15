<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitorSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'landing_page',
        'last_activity',
    ];

    protected function casts(): array
    {
        return [
            'last_activity' => 'datetime',
        ];
    }

    public function apiCalls(): HasMany
    {
        return $this->hasMany(ApiCall::class);
    }
}