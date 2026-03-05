<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(20);
        
        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        
        return view('restaurant.customers.index', compact('customers', 'currencySymbol'));
    }

    public function create()
    {
        return view('restaurant.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:255|unique:customers,email,NULL,id,tenant_id,' . auth()->user()->tenant_id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'dietary_preferences' => 'nullable|string',
        ]);

        Customer::create([
            'tenant_id' => auth()->user()->tenant_id,
            ...$validated,
        ]);

        return redirect()->route('restaurant.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders', 'reservations']);
        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        return view('restaurant.customers.show', compact('customer', 'currencySymbol'));
    }

    public function edit(Customer $customer)
    {
        return view('restaurant.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id . ',id,tenant_id,' . auth()->user()->tenant_id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'dietary_preferences' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('restaurant.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->update(['is_active' => false]);
        return redirect()->route('restaurant.customers.index')
            ->with('success', 'Customer deactivated successfully.');
    }
}
