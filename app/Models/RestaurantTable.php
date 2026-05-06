<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantTable extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'floor_section',
        'capacity',
        'position_x',
        'position_y',
        'status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class);
    }

    public function activeOrders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served']);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
