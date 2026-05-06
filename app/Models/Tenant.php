<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Plan;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'status',
        'plan',
        'pos_type',
        'payhere_customer_id',
        'billing_email',
    ];

    /**
     * Generate a unique slug from company name (e.g. "Acme Grocers" -> "acme-grocers" or "acme-grocers-2" if taken).
     */
    public static function generateUniqueSlug(string $companyName): string
    {
        $base = Str::slug(Str::limit($companyName, 40, ''));
        if ($base === '') {
            $base = 'store';
        }
        $slug = $base;
        $counter = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function businessSetting()
    {
        return $this->hasOne(BusinessSetting::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->whereIn('status', ['active', 'trialing', 'pending_payment'])->latestOfMany();
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->whereIn('status', ['active', 'trialing', 'pending_payment', 'past_due'])->latestOfMany();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isSubscribed(): bool
    {
        return $this->activeSubscription && $this->activeSubscription->isActive();
    }

    public function isOnTrial(): bool
    {
        return $this->activeSubscription && $this->activeSubscription->isOnTrial();
    }

    public function needsPayment(): bool
    {
        $sub = $this->currentSubscription;
        return $sub && $sub->isPendingPayment();
    }
}
