<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FriendLink extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'site_name',
        'site_url',
        'description',
        'logo_url',
        'sort_order',
        'status',
    ];

    public function friendLinkApplications(): HasMany
    {
        return $this->hasMany(FriendLinkApplication::class);
    }
}