<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\Supplier;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{
    public function index()
    {
        $grns = Grn::with(['supplier', 'branch', 'user'])->latest()->get();
        return view('grns.index', compact('grns'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $branches = Branch::all();
        $products = Product::all();
        return view('grns.create', compact('suppliers', 'branches', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'branch_id' => 'required|exists:branches,id',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $grn = Grn::create([
                'supplier_id' => $validated['supplier_id'],
                'branch_id' => $validated['branch_id'],
                'user_id' => auth()->id(),
                'grn_number' => 'GRN-' . strtoupper(uniqid()),
                'received_date' => $validated['received_date'],
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['items'] as $item) {
                GrnItem::create([
                    'grn_id' => $grn->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('grns.index')->with('success', 'GRN created as draft.');
    }

    public function show(Grn $grn)
    {
        $grn->load(['supplier', 'branch', 'user', 'items.product']);
        return view('grns.show', compact('grn'));
    }

    public function receive(Grn $grn)
    {
        if ($grn->status !== 'draft') {
            return back()->with('error', 'Only draft GRNs can be received.');
        }

        DB::transaction(function () use ($grn) {
            foreach ($grn->items as $item) {
                $stock = Stock::firstOrCreate(
                    [
                        'product_id' => $item->product_id,
                        'branch_id' => $grn->branch_id,
                        'tenant_id' => $grn->tenant_id,
                    ],
                    [
                        'quantity' => 0,
                        'low_stock_threshold' => 10,
                    ]
                );

                $stock->increment('quantity', $item->quantity);
            }

            $grn->update(['status' => 'received']);
        });

        return redirect()->route('grns.index')->with('success', 'GRN received and stock updated.');
    }

    public function destroy(Grn $grn)
    {
        if ($grn->status === 'received') {
            return back()->with('error', 'Cannot delete a received GRN.');
        }

        $grn->items()->delete();
        $grn->delete();

        return redirect()->route('grns.index')->with('success', 'GRN deleted.');
    }
}
