<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_address' => ['required', 'string', 'max:255'],
            'shop_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = \DB::transaction(function () use ($request) {
            // 1. Create Tenant
            $tenant = \App\Models\Tenant::create([
                'name' => $request->shop_name,
                'email' => $request->email,
                'phone' => $request->shop_phone,
                'address' => $request->shop_address,
                'status' => 'active',
            ]);

            // 2. Create Default Branch
            $branch = \App\Models\Branch::create([
                'tenant_id' => $tenant->id,
                'name' => 'Main Branch',
                'address' => $request->shop_address,
                'phone' => $request->shop_phone,
            ]);

            // 3. Create Owner User
            $user = User::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            // 4. Assign Role
            $user->assignRole('business_owner');

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
