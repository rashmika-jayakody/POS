<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('products')->get();
        return view('units.index', compact('units'));
    }

    public function create(Request $request)
    {
        $returnTo = $request->query('return');
        return view('units.create', compact('returnTo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'required|string|max:20',
        ]);

        Unit::create([
            'name' => $validated['name'],
            'short_code' => $validated['short_code'],
        ]);

        $returnTo = $request->query('return') ?? $request->input('return');
        if ($returnTo === 'products.create') {
            return redirect()->route('products.create')->with('success', 'Unit added. You can now select it when creating a product.');
        }

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'required|string|max:20',
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->count() > 0) {
            return back()->with('error', 'Cannot delete unit that is used by products.');
        }

        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
