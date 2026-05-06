<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'dietary_preferences',
        'favorite_items',
        'loyalty_points',
        'lifetime_spent',
        'visit_count',
        'last_visit_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_visit_at' => 'datetime',
        'loyalty_points' => 'integer',
        'lifetime_spent' => 'decimal:2',
        'visit_count' => 'integer',
        'is_active' => 'boolean',
        'favorite_items' => 'array',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
    }

    public function addVisit(): void
    {
        $this->increment('visit_count');
        $this->update(['last_visit_at' => now()]);
    }
}
