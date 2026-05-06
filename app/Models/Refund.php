<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'user_id',
        'refund_number',
        'type',
        'original_sale_id',
        'original_order_id',
        'original_invoice_no',
        'reason',
        'reason_notes',
        'subtotal',
        'tax_total',
        'grand_total',
        'refund_method',
        'inventory_updated',
        'cash_drawer_session_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'inventory_updated' => 'boolean',
    ];

    public const REASONS = [
        'damaged' => 'Damaged Product',
        'wrong_item' => 'Wrong Item',
        'customer_request' => 'Customer Request',
        'quality_issue' => 'Quality Issue',
        'other' => 'Other',
    ];

    public const REFUND_METHODS = [
        'cash' => 'Cash',
        'card' => 'Card',
        'store_credit' => 'Store Credit',
        'original_method' => 'Original Payment Method',
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
        return $this->hasMany(RefundItem::class);
    }

    public function cashDrawerSession()
    {
        return $this->belongsTo(CashDrawerSession::class);
    }

    public function originalSale()
    {
        return $this->belongsTo(Sale::class, 'original_sale_id');
    }

    public function originalOrder()
    {
        return $this->belongsTo(RestaurantOrder::class, 'original_order_id');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public static function generateRefundNumber(): string
    {
        return 'REF-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
