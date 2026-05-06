<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'user_id',
        'invoice_no',
        'sale_date',
        'subtotal',
        'discount_total',
        'tax_total',
        'grand_total',
        'payment_method',
        'cash_drawer_session_id',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function cashDrawerSession()
    {
        return $this->belongsTo(CashDrawerSession::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'original_sale_id');
    }
}
