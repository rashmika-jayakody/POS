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

    /* ─── Plan Configuration Helpers ─── */

    /**
     * Get the current plan's configuration.
     */
    public function getPlanConfigAttribute()
    {
        return Plan::where('slug', $this->plan)->first() ?? Plan::where('slug', 'starter')->first();
    }

    /**
     * Check if the tenant's plan includes a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        $plan = $this->plan_config;
        return in_array($feature, $plan->features ?? []);
    }

    /**
     * Check if the tenant has reached is within the limit for a specific metric.
     * Metrics: 'max_branches', 'max_users'
     */
    public function isWithinLimit(string $metric): bool
    {
        $plan = $this->plan_config;
        $limit = $plan->$metric ?? -1;

        if ($limit === -1) {
            return true;
        }

        $currentCount = 0;
        if ($metric === 'max_branches') {
            $currentCount = $this->branches()->count();
        } elseif ($metric === 'max_users') {
            $currentCount = $this->users()->count();
        }

        return $currentCount < $limit;
    }
}
