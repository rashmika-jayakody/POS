<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderModifier extends Model
{
    protected $fillable = [
        'restaurant_order_id',
        'order_item_id',
        'modifier_type',
        'name',
        'price_adjustment',
        'description',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrderItem::class, 'order_item_id');
    }
}
