<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestaurantTableController extends Controller
{
    public function index(): View
    {
        $tables = RestaurantTable::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('floor_section')
            ->orderBy('name')
            ->get();
        
        return view('restaurant.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('restaurant.tables.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'floor_section' => 'nullable|string|max:100',
            'capacity' => 'required|integer|min:1|max:50',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        RestaurantTable::create([
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => auth()->user()->branch_id,
            ...$validated,
        ]);

        return redirect()->route('restaurant.tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function show(RestaurantTable $table)
    {
        //
    }

    public function edit(RestaurantTable $table)
    {
        return view('restaurant.tables.edit', compact('table'));
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'floor_section' => 'nullable|string|max:100',
            'capacity' => 'required|integer|min:1|max:50',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'status' => 'required|in:available,occupied,reserved,cleaning',
            'notes' => 'nullable|string',
        ]);

        $table->update($validated);

        return redirect()->route('restaurant.tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $table)
    {
        $table->update(['is_active' => false]);
        return redirect()->route('restaurant.tables.index')
            ->with('success', 'Table deactivated successfully.');
    }
}
