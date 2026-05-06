<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashDrawerSession extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'user_id',
        'session_number',
        'status',
        'opening_balance',
        'closing_balance',
        'expected_balance',
        'cash_sales',
        'card_sales',
        'other_sales',
        'refunds_total',
        'cash_added',
        'cash_removed',
        'variance',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'card_sales' => 'decimal:2',
        'other_sales' => 'decimal:2',
        'refunds_total' => 'decimal:2',
        'cash_added' => 'decimal:2',
        'cash_removed' => 'decimal:2',
        'variance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cash_drawer_session_id');
    }

    public function restaurantOrders()
    {
        return $this->hasMany(RestaurantOrder::class, 'cash_drawer_session_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function calculateExpectedBalance(): float
    {
        return (float) $this->opening_balance
            + (float) $this->cash_sales
            + (float) $this->cash_added
            - (float) $this->cash_removed
            - (float) $this->refunds_total;
    }

    public function calculateVariance(): float
    {
        if ($this->closing_balance === null) {
            return 0;
        }

        return (float) $this->closing_balance - $this->calculateExpectedBalance();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public static function generateSessionNumber(): string
    {
        return 'SES-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}
