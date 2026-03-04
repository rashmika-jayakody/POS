<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\View\View;

class StoreLandingController extends Controller
{
    /**
     * Show the store landing / sign-in page for a tenant (path: /app/{slug}).
     */
    public function show(Tenant $tenant): View
    {
        if ($tenant->status !== 'active') {
            abort(404);
        }
        return view('store.landing', ['tenant' => $tenant]);
    }
}
