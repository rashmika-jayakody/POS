<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessSettingsController extends Controller
{
    private function authorizeBusinessSettings(): void
    {
        if (! auth()->user()->hasAnyRole(['business_owner', 'system_owner'])) {
            abort(403, 'Only business owners can manage business settings.');
        }
    }

    public function edit()
    {
        $this->authorizeBusinessSettings();
        $tenant = auth()->user()->tenant;
        if (! $tenant) {
            abort(403, 'No business associated with your account.');
        }

        $settings = $tenant->businessSetting ?? new BusinessSetting(['tenant_id' => $tenant->id]);
        return view('business-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorizeBusinessSettings();
        $tenant = auth()->user()->tenant;
        if (! $tenant) {
            abort(403, 'No business associated with your account.');
        }

        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,webp'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_label' => ['nullable', 'string', 'max:50'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'receipt_header' => ['nullable', 'string', 'max:500'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
        ]);

        $settings = $tenant->businessSetting ?? new BusinessSetting(['tenant_id' => $tenant->id]);

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }
        unset($validated['logo']);

        $settings->fill($validated);
        $settings->tenant_id = $tenant->id;
        $settings->save();

        ActivityLogService::log('business_settings_updated', 'Business settings updated', ['tenant_id' => $tenant->id]);

        return redirect()->route('business-settings.edit')
            ->with('success', 'Business settings updated successfully.');
    }
}
