<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    protected $fillable = [
        'refund_id',
        'product_id',
        'qty',
        'unit_price',
        'line_total',
        'cost_price_at_refund',
        'restocked',
        'notes',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'cost_price_at_refund' => 'decimal:2',
        'restocked' => 'boolean',
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
