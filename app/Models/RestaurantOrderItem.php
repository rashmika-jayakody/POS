<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantOrderItem extends Model
{
    protected $fillable = [
        'restaurant_order_id',
        'product_id',
        'qty',
        'unit_price',
        'modifier_total',
        'discount_amount',
        'line_total',
        'special_instructions',
        'status',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'modifier_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(OrderModifier::class, 'order_item_id');
    }

    public function updateStatus(string $status): void
    {
        $this->update(['status' => $status]);
    }
}
