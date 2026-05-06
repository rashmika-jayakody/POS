<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'unit_id',
        'name',
        'code',
        'barcode',
        'cost_price',
        'selling_price',
        'discount_type',
        'discount_value',
        'image_url',
        'is_active',
        'has_modifiers',
        'modifier_groups',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'has_modifiers' => 'boolean',
        'modifier_groups' => 'array',
    ];

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function modifiers()
    {
        return $this->hasMany(ProductModifier::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function allModifiers()
    {
        return $this->hasMany(ProductModifier::class)->orderBy('sort_order');
    }

    public function refundItems()
    {
        return $this->hasMany(RefundItem::class);
    }
}
