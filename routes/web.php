<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashDrawerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

    // Cash Drawer Routes
    Route::get('/cash-drawer', [CashDrawerController::class, 'index'])->name('cash-drawer.index');
    Route::post('/cash-drawer/open', [CashDrawerController::class, 'open'])->name('cash-drawer.open');
    Route::post('/cash-drawer/close', [CashDrawerController::class, 'close'])->name('cash-drawer.close');
    Route::get('/cash-drawer/status', [CashDrawerController::class, 'status'])->name('cash-drawer.status');

    // Management
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('branches', \App\Http\Controllers\BranchController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'edit', 'update']);

    // Product & Stock Management
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);

    // Supplier & GRN Management
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('grns', \App\Http\Controllers\GrnController::class);
    Route::post('/grns/{grn}/receive', [\App\Http\Controllers\GrnController::class, 'receive'])->name('grns.receive');
});


require __DIR__ . '/auth.php';
