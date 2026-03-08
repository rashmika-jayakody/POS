<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantOrder extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'restaurant_table_id',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'order_no',
        'order_type',
        'status',
        'guest_count',
        'special_instructions',
        'dietary_preferences',
        'subtotal',
        'discount_total',
        'tax_total',
        'service_charge',
        'grand_total',
        'is_split',
        'split_count',
        'confirmed_at',
        'preparing_at',
        'ready_at',
        'served_at',
        'completed_at',
    ];

    protected $casts = [
        'guest_count' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'is_split' => 'boolean',
        'split_count' => 'integer',
        'confirmed_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'served_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(OrderModifier::class);
    }

    public function updateStatus(string $status): void
    {
        $this->update([
            'status' => $status,
            $status . '_at' => now(),
        ]);
    }

    public static function generateOrderNo(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = self::where('order_no', 'like', "ORD-{$date}-%")
            ->orderBy('order_no', 'desc')
            ->first();
        
        $seq = 1;
        if ($lastOrder) {
            $parts = explode('-', $lastOrder->order_no);
            $seq = (int) end($parts) + 1;
        }
        
        return 'ORD-' . $date . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
