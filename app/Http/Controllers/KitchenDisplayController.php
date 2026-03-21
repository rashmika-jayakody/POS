<?php

namespace App\Http\Controllers;

use App\Models\RestaurantOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenDisplayController extends Controller
{
    public function index(): View
    {
        $orders = RestaurantOrder::where('tenant_id', auth()->user()->tenant_id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with(['table', 'items.product', 'items.modifiers'])
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'confirmed' THEN 2 
                WHEN status = 'preparing' THEN 3 
                WHEN status = 'ready' THEN 4 
                ELSE 5 
            END")
            ->orderBy('confirmed_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        return view('restaurant.kitchen.index', compact('orders'));
    }
}
