<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'payhere_payment_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'plan',
        'payhere_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payhere_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }
}