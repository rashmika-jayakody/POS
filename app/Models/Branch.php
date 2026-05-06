<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'phone',
        'is_active',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function cashDrawerSessions()
    {
        return $this->hasMany(CashDrawerSession::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function restaurantOrders()
    {
        return $this->hasMany(RestaurantOrder::class);
    }
}
