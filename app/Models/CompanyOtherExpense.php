<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\BelongsToTenant;

class CompanyOtherExpense extends Model
{
    use BelongsToTenant;

    /** Category key for owner withdrawals — reduces cash but NOT profit. Never mix with business expenses. */
    public const CATEGORY_OWNER_DRAWINGS = 'Owner Drawings';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'category',
        'description',
        'amount',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Business expense categories (reduce profit, appear in P&L).
     * Sub-categories: Utilities, Rent, Salary, Transport, Repairs, etc.
     */
    public static function businessExpenseCategories(): array
    {
        return [
            'Utilities' => 'Utilities (Light, Water, Internet)',
            'Rent' => 'Rent',
            'Salaries' => 'Salary',
            'Transport' => 'Transport',
            'Repairs' => 'Repairs',
            'Office' => 'Office Supplies',
            'Marketing' => 'Marketing',
            'Maintenance' => 'Maintenance',
            'Insurance' => 'Insurance',
            'Other' => 'Other',
        ];
    }

    /**
     * Owner Drawings only — recorded and reduces cash, but NOT included in expense reports / profit.
     */
    public static function ownerDrawingsCategory(): array
    {
        return [self::CATEGORY_OWNER_DRAWINGS => 'Owner Withdrawals (Drawings)'];
    }

    /** All categories for validation: business + owner drawings. */
    public static function allCategoryKeys(): array
    {
        return array_merge(
            array_keys(self::businessExpenseCategories()),
            array_keys(self::ownerDrawingsCategory())
        );
    }

    public function isOwnerDrawing(): bool
    {
        return $this->category === self::CATEGORY_OWNER_DRAWINGS;
    }

    /** Scope: only business expenses (exclude owner drawings) — use for P&L. */
    public function scopeBusinessExpenses(Builder $query): Builder
    {
        return $query->where('category', '!=', self::CATEGORY_OWNER_DRAWINGS);
    }

    /** Scope: only owner drawings. */
    public function scopeOwnerDrawings(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_OWNER_DRAWINGS);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
