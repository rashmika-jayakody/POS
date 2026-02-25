<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'tenant_id',
        'logo_path',
        'business_name',
        'currency_code',
        'currency_symbol',
        'tax_rate',
        'tax_label',
        'primary_color',
        'secondary_color',
        'accent_color',
        'address',
        'phone',
        'email',
        'website',
        'receipt_header',
        'receipt_footer',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Display name for the business (settings name or tenant name fallback).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->business_name ?: $this->tenant?->name ?? config('app.name');
    }
}
