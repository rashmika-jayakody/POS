<?php

use App\Models\BusinessSetting;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $tenantIdsWithSettings = BusinessSetting::pluck('tenant_id')->toArray();
        Tenant::whereNotIn('id', $tenantIdsWithSettings)->each(function (Tenant $tenant) {
            BusinessSetting::create([
                'tenant_id' => $tenant->id,
                'business_name' => $tenant->name,
                'address' => $tenant->address,
                'phone' => $tenant->phone,
                'email' => $tenant->email,
                'currency_code' => 'LKR',
                'currency_symbol' => 'Rs',
            ]);
        });
    }

    public function down(): void
    {
        // Optional: remove settings that were created by this migration (hard to track)
        // Leave as no-op so we don't delete user-created data
    }
};
