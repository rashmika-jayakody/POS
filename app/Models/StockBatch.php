<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class StockBatch extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'branch_id',
        'batch_number',
        'quantity',
        'received_at',
        'expiry_date',
        'grn_item_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'received_at' => 'date',
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function grnItem()
    {
        return $this->belongsTo(GrnItem::class, 'grn_item_id');
    }
}
