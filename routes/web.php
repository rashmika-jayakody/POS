<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashDrawerController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\OnboardingWizardController;
use App\Http\Controllers\StoreLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public onboarding: pricing → wizard → create tenant (path-based: /app/{slug} later)
Route::get('/onboarding', [OnboardingWizardController::class, 'index'])->name('onboarding.index');
Route::post('/onboarding', [OnboardingWizardController::class, 'store'])->name('onboarding.store');

// Store landing by slug (path-based tenancy): /app/acme → sign in to that store
Route::get('/app/{tenant:slug}', [StoreLandingController::class, 'show'])->name('store.landing');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// SaaS Platform Management (System Owner only)
Route::middleware(['auth', 'role:system_owner'])->group(function () {
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cash Drawer / POS
    Route::get('/cash-drawer', [CashDrawerController::class, 'index'])->name('cash-drawer.index');
    Route::patch('/cash-drawer/shortcuts', [CashDrawerController::class, 'updateShortcuts'])->name('cash-drawer.shortcuts.update');
    Route::get('/customers/search', function (\Illuminate\Http\Request $r) {
        return response()->json([]);
    })->name('customers.search');
    Route::post('/cash-drawer/open', [CashDrawerController::class, 'open'])->name('cash-drawer.open');
    Route::post('/cash-drawer/close', [CashDrawerController::class, 'close'])->name('cash-drawer.close');
    Route::get('/cash-drawer/status', [CashDrawerController::class, 'status'])->name('cash-drawer.status');
    Route::post('/cash-drawer/process-return', [CashDrawerController::class, 'processReturn'])->name('cash-drawer.process-return');

    // Business settings (business owner / system owner)
    Route::get('/business-settings', [\App\Http\Controllers\BusinessSettingsController::class, 'edit'])->name('business-settings.edit');
    Route::patch('/business-settings', [\App\Http\Controllers\BusinessSettingsController::class, 'update'])->name('business-settings.update');

    // Activity log (permission: view activity log)
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:view activity log');

    // Management
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('branches', \App\Http\Controllers\BranchController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    // Product & Stock Management
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('units', \App\Http\Controllers\UnitController::class);

    // Supplier & GRN Management
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('grns', \App\Http\Controllers\GrnController::class);
    Route::post('/grns/{grn}/receive', [\App\Http\Controllers\GrnController::class, 'receive'])->name('grns.receive');
});


require __DIR__ . '/auth.php';
