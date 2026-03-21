<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashDrawerController;
use App\Http\Controllers\RestaurantCashDrawerController;
use App\Http\Controllers\RestaurantTableController;
use App\Http\Controllers\RestaurantOrderController;
use App\Http\Controllers\KitchenDisplayController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\OnboardingWizardController;
use App\Http\Controllers\StoreLandingController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = \App\Models\Plan::where('is_active', true)->get();
    return view('welcome', compact('plans'));
});

Route::get('/about', function () {
    return view('about');
})->name('about');

// Switch language (stored in session; optional: set APP_LOCALE in .env for app-wide default)
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'si'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale.switch');

// Public onboarding: pricing → wizard → create tenant (path-based: /app/{slug} later)
Route::get('/onboarding', [OnboardingWizardController::class, 'index'])->name('onboarding.index');
Route::post('/onboarding', [OnboardingWizardController::class, 'store'])->name('onboarding.store');
Route::post('/onboarding/validate-step', [OnboardingWizardController::class, 'validateStep'])->name('onboarding.validate-step');
Route::post('/onboarding/resend-code', [OnboardingWizardController::class, 'resendCode'])->name('onboarding.resend-code');

// Store landing by slug (path-based tenancy): /app/acme → sign in to that store
Route::get('/app/{tenant:slug}', [StoreLandingController::class, 'show'])->name('store.landing');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// SaaS Platform Management (System Owner only)
Route::middleware(['auth', 'role:system_owner'])->group(function () {
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
    Route::get('/translations', [\App\Http\Controllers\TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [\App\Http\Controllers\TranslationController::class, 'store'])->name('translations.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/pricing', [\App\Http\Controllers\PricingController::class, 'index'])->name('pricing.index');
    Route::put('/pricing/{slug}', [\App\Http\Controllers\PricingController::class, 'update'])->name('pricing.update');
    // Cash Drawer / POS (Retail)
    Route::get('/cash-drawer', [CashDrawerController::class, 'index'])->name('cash-drawer.index');
    Route::patch('/cash-drawer/shortcuts', [CashDrawerController::class, 'updateShortcuts'])->name('cash-drawer.shortcuts.update');
    Route::post('/cash-drawer/open', [CashDrawerController::class, 'open'])->name('cash-drawer.open');
    Route::post('/cash-drawer/close', [CashDrawerController::class, 'close'])->name('cash-drawer.close');
    Route::get('/cash-drawer/status', [CashDrawerController::class, 'status'])->name('cash-drawer.status');
    Route::get('/cash-drawer/stock', [CashDrawerController::class, 'getStock'])->name('cash-drawer.stock');
    Route::post('/cash-drawer/process-sale', [CashDrawerController::class, 'processSale'])->name('cash-drawer.process-sale');
    Route::post('/cash-drawer/process-return', [CashDrawerController::class, 'processReturn'])->name('cash-drawer.process-return');

    // Restaurant Cash Drawer / POS
    Route::get('/restaurant-cash-drawer', [RestaurantCashDrawerController::class, 'index'])->name('restaurant-cash-drawer.index');
    Route::patch('/restaurant-cash-drawer/shortcuts', [RestaurantCashDrawerController::class, 'updateShortcuts'])->name('restaurant-cash-drawer.shortcuts.update');
    Route::post('/restaurant-cash-drawer/open', [RestaurantCashDrawerController::class, 'open'])->name('restaurant-cash-drawer.open');
    Route::post('/restaurant-cash-drawer/close', [RestaurantCashDrawerController::class, 'close'])->name('restaurant-cash-drawer.close');
    Route::get('/restaurant-cash-drawer/status', [RestaurantCashDrawerController::class, 'status'])->name('restaurant-cash-drawer.status');
    Route::post('/restaurant-cash-drawer/process-return', [RestaurantCashDrawerController::class, 'processReturn'])->name('restaurant-cash-drawer.process-return');

    Route::get('/customers/search', function (\Illuminate\Http\Request $r) {
        return response()->json([]);
    })->name('customers.search')->middleware('plan_feature:loyalty_customers');

    // Business settings (business owner / system owner)
    Route::get('/business-settings', [\App\Http\Controllers\BusinessSettingsController::class, 'edit'])->name('business-settings.edit');
    Route::patch('/business-settings', [\App\Http\Controllers\BusinessSettingsController::class, 'update'])->name('business-settings.update');

    // Activity log (permission: view activity log)
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:view activity log');

    // Management
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware('plan_limit:max_users');
    Route::resource('branches', \App\Http\Controllers\BranchController::class)->middleware('plan_limit:max_branches');
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'create', 'store', 'edit', 'update'])->middleware('plan_feature:staff_permission_roles');

    // Product & Stock Management
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('units', \App\Http\Controllers\UnitController::class);

    // Supplier & GRN Management
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->middleware('plan_feature:supplier_management');
    Route::resource('grns', \App\Http\Controllers\GrnController::class);
    Route::post('/grns/{grn}/receive', [\App\Http\Controllers\GrnController::class, 'receive'])->name('grns.receive');

    // Company Other Expenses
    Route::resource('company-other-expenses', \App\Http\Controllers\CompanyOtherExpenseController::class)->except(['show'])->middleware('plan_feature:other_expenses');

    // Restaurant Features
    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::resource('tables', RestaurantTableController::class);
        Route::resource('orders', RestaurantOrderController::class);
        Route::resource('reservations', ReservationController::class);
        Route::resource('customers', CustomerController::class)->middleware('plan_feature:loyalty_customers');
        Route::get('/kitchen', [KitchenDisplayController::class, 'index'])->name('kitchen.index');
        Route::post('/orders/{order}/update-status', [RestaurantOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{order}/pay', [RestaurantOrderController::class, 'pay'])->name('orders.pay');
        Route::post('/orders/{order}/split', [RestaurantOrderController::class, 'splitBill'])->name('orders.split');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/sales-summary', [ReportsController::class, 'salesSummary'])->name('sales-summary');
        Route::get('/profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss')->middleware('plan_feature:profit_loss_report');
        Route::get('/itemwise-sales', [ReportsController::class, 'itemwiseSales'])->name('itemwise-sales')->middleware('plan_feature:item_wise_sales_report');
        Route::get('/categorywise-sales', [ReportsController::class, 'categorywiseSales'])->name('categorywise-sales')->middleware('plan_feature:category_wise_sales_report');
        Route::get('/cash-summary', [ReportsController::class, 'cashSummary'])->name('cash-summary');
        Route::get('/expiry-tracking', [ReportsController::class, 'expiryTracking'])->name('expiry-tracking')->middleware('plan_feature:expiry_tracking');
        Route::get('/stock-valuation', [ReportsController::class, 'stockValuation'])->name('stock-valuation');
    });
});


require __DIR__ . '/auth.php';
