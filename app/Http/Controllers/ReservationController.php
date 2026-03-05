<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(): View
    {
        $reservations = Reservation::where('tenant_id', auth()->user()->tenant_id)
            ->with(['table', 'customer'])
            ->orderBy('reservation_date', 'desc')
            ->paginate(20);
        
        return view('restaurant.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $tables = RestaurantTable::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->get();
        return view('restaurant.reservations.create', compact('tables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_table_id' => 'nullable|exists:restaurant_tables,id',
            'customer_name' => 'required|string|max:200',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date|after:now',
            'guest_count' => 'required|integer|min:1|max:50',
            'special_requests' => 'nullable|string',
        ]);

        Reservation::create([
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => auth()->user()->branch_id,
            'reservation_no' => Reservation::generateReservationNo(),
            ...$validated,
        ]);

        return redirect()->route('restaurant.reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['table', 'customer']);
        return view('restaurant.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $tables = RestaurantTable::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->get();
        return view('restaurant.reservations.edit', compact('reservation', 'tables'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'restaurant_table_id' => 'nullable|exists:restaurant_tables,id',
            'customer_name' => 'required|string|max:200',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date',
            'guest_count' => 'required|integer|min:1|max:50',
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show',
            'special_requests' => 'nullable|string',
        ]);

        $reservation->update($validated);

        return redirect()->route('restaurant.reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('restaurant.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
}
