<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CashDrawerController;
use App\Http\Controllers\CashDrawerSessionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KitchenDisplayController;
use App\Http\Controllers\OnboardingWizardController;
use App\Http\Controllers\PayHereWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantCashDrawerController;
use App\Http\Controllers\RestaurantOrderController;
use App\Http\Controllers\RestaurantTableController;
use App\Http\Controllers\StoreLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'si'])) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('locale.switch');

Route::get('/onboarding', [OnboardingWizardController::class, 'index'])->name('onboarding.index');
Route::post('/onboarding', [OnboardingWizardController::class, 'store'])->name('onboarding.store');
Route::post('/onboarding/validate-step', [OnboardingWizardController::class, 'validateStep'])->name('onboarding.validate-step');
Route::post('/onboarding/resend-code', [OnboardingWizardController::class, 'resendCode'])->name('onboarding.resend-code');

Route::get('/app/{tenant:slug}', [StoreLandingController::class, 'show'])->name('store.landing');

Route::post('/payhere/notify', [PayHereWebhookController::class, 'handleNotify'])->name('payhere.webhook');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:system_owner'])->group(function () {
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
    Route::get('/translations', [\App\Http\Controllers\TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [\App\Http\Controllers\TranslationController::class, 'store'])->name('translations.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/checkout/{plan}', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/return', [BillingController::class, 'return'])->name('billing.return');
    Route::get('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('/billing/cancel-subscription', [BillingController::class, 'cancelSubscription'])->name('billing.cancel-subscription');
    Route::get('/billing/switch/{plan}', [BillingController::class, 'switchPlan'])->name('billing.switch');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cash Drawer / POS (Retail)
    Route::get('/cash-drawer', [CashDrawerController::class, 'index'])->name('cash-drawer.index');
    Route::patch('/cash-drawer/shortcuts', [CashDrawerController::class, 'updateShortcuts'])->name('cash-drawer.shortcuts.update');
    Route::post('/cash-drawer/open', [CashDrawerController::class, 'open'])->name('cash-drawer.open');
    Route::post('/cash-drawer/close', [CashDrawerController::class, 'close'])->name('cash-drawer.close');
    Route::get('/cash-drawer/status', [CashDrawerController::class, 'status'])->name('cash-drawer.status');
    Route::get('/cash-drawer/stock', [CashDrawerController::class, 'getStock'])->name('cash-drawer.stock');
    Route::post('/cash-drawer/process-sale', [CashDrawerController::class, 'processSale'])->name('cash-drawer.process-sale');
    Route::post('/cash-drawer/process-return', [CashDrawerController::class, 'processReturn'])->name('cash-drawer.process-return');
    Route::get('/cash-drawer/print-receipt/{sale}', [CashDrawerController::class, 'printReceipt'])->name('cash-drawer.print-receipt');

    // Cash Drawer Sessions
    Route::prefix('cash-drawer-sessions')->name('cash-drawer-sessions.')->group(function () {
        Route::get('/', [CashDrawerSessionController::class, 'index'])->name('index');
        Route::post('/open', [CashDrawerSessionController::class, 'open'])->name('open');
        Route::post('/{session}/close', [CashDrawerSessionController::class, 'close'])->name('close');
        Route::get('/{session}', [CashDrawerSessionController::class, 'show'])->name('show');
        Route::get('/status', [CashDrawerSessionController::class, 'status'])->name('status');
        Route::post('/{session}/add-cash', [CashDrawerSessionController::class, 'addCash'])->name('add-cash');
        Route::post('/{session}/remove-cash', [CashDrawerSessionController::class, 'removeCash'])->name('remove-cash');
    });

    // Refunds
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [RefundController::class, 'index'])->name('index');
        Route::get('/create', [RefundController::class, 'create'])->name('create');
        Route::post('/', [RefundController::class, 'store'])->name('store');
        Route::get('/{refund}', [RefundController::class, 'show'])->name('show');
        Route::post('/search-invoice', [RefundController::class, 'searchInvoice'])->name('search-invoice');
        Route::get('/{refund}/print', [RefundController::class, 'printReceipt'])->name('print');
    });

    // Restaurant Cash Drawer / POS
    Route::get('/restaurant-cash-drawer', [RestaurantCashDrawerController::class, 'index'])->name('restaurant-cash-drawer.index');
    Route::patch('/restaurant-cash-drawer/shortcuts', [RestaurantCashDrawerController::class, 'updateShortcuts'])->name('restaurant-cash-drawer.shortcuts.update');
    Route::post('/restaurant-cash-drawer/open', [RestaurantCashDrawerController::class, 'open'])->name('restaurant-cash-drawer.open');
    Route::post('/restaurant-cash-drawer/close', [RestaurantCashDrawerController::class, 'close'])->name('restaurant-cash-drawer.close');
    Route::get('/restaurant-cash-drawer/status', [RestaurantCashDrawerController::class, 'status'])->name('restaurant-cash-drawer.status');
    Route::post('/restaurant-cash-drawer/process-return', [RestaurantCashDrawerController::class, 'processReturn'])->name('restaurant-cash-drawer.process-return');
    Route::get('/restaurant-cash-drawer/session-status', [RestaurantCashDrawerController::class, 'getSessionStatus'])->name('restaurant-cash-drawer.session-status');
    Route::post('/restaurant-cash-drawer/open-session', [RestaurantCashDrawerController::class, 'openSession'])->name('restaurant-cash-drawer.open-session');
    Route::post('/restaurant-cash-drawer/close-session', [RestaurantCashDrawerController::class, 'closeSession'])->name('restaurant-cash-drawer.close-session');
    Route::post('/restaurant-cash-drawer/create-order', [RestaurantCashDrawerController::class, 'createOrder'])->name('restaurant-cash-drawer.create-order');
    Route::post('/restaurant-cash-drawer/pay-order/{orderNo}', [RestaurantCashDrawerController::class, 'payOrder'])->name('restaurant-cash-drawer.pay-order');
    Route::get('/restaurant-cash-drawer/print-receipt/{orderNo}', [RestaurantCashDrawerController::class, 'printReceipt'])->name('restaurant-cash-drawer.print-receipt');
    Route::post('/restaurant-cash-drawer/print-kitchen/{orderNo}', [RestaurantCashDrawerController::class, 'printKitchenTicket'])->name('restaurant-cash-drawer.print-kitchen');

    Route::get('/customers/search', function (\Illuminate\Http\Request $r) {
        return response()->json([]);
    })->name('customers.search');

    Route::get('/business-settings', [\App\Http\Controllers\BusinessSettingsController::class, 'edit'])->name('business-settings.edit');
    Route::patch('/business-settings', [\App\Http\Controllers\BusinessSettingsController::class, 'update'])->name('business-settings.update');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:view activity log');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('branches', \App\Http\Controllers\BranchController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('units', \App\Http\Controllers\UnitController::class);

    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('grns', \App\Http\Controllers\GrnController::class);
    Route::post('/grns/{grn}/receive', [\App\Http\Controllers\GrnController::class, 'receive'])->name('grns.receive');

    Route::resource('company-other-expenses', \App\Http\Controllers\CompanyOtherExpenseController::class)->except(['show']);

    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::resource('tables', RestaurantTableController::class);
        Route::resource('orders', RestaurantOrderController::class);
        Route::resource('reservations', ReservationController::class);
        Route::resource('customers', CustomerController::class);
        Route::get('/kitchen', [KitchenDisplayController::class, 'index'])->name('kitchen.index');
        Route::post('/orders/{order}/update-status', [RestaurantOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{order}/pay', [RestaurantOrderController::class, 'pay'])->name('orders.pay');
        Route::post('/orders/{order}/split', [RestaurantOrderController::class, 'splitBill'])->name('orders.split');
        Route::post('/orders/{order}/print-kitchen', [RestaurantOrderController::class, 'printKitchen'])->name('orders.print-kitchen');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/sales-summary', [ReportsController::class, 'salesSummary'])->name('sales-summary');
        Route::get('/profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/itemwise-sales', [ReportsController::class, 'itemwiseSales'])->name('itemwise-sales');
        Route::get('/categorywise-sales', [ReportsController::class, 'categorywiseSales'])->name('categorywise-sales');
        Route::get('/cash-summary', [ReportsController::class, 'cashSummary'])->name('cash-summary');
        Route::get('/expiry-tracking', [ReportsController::class, 'expiryTracking'])->name('expiry-tracking');
        Route::get('/stock-valuation', [ReportsController::class, 'stockValuation'])->name('stock-valuation');
        Route::get('/refunds', [ReportsController::class, 'refunds'])->name('refunds');
        Route::get('/employee-performance', [ReportsController::class, 'employeePerformance'])->name('employee-performance');
        Route::get('/restaurant-sales-summary', [ReportsController::class, 'restaurantSalesSummary'])->name('restaurant-sales-summary');
        Route::get('/restaurant-profit-loss', [ReportsController::class, 'restaurantProfitLoss'])->name('restaurant-profit-loss');
        Route::get('/restaurant-itemwise-sales', [ReportsController::class, 'restaurantItemwiseSales'])->name('restaurant-itemwise-sales');
        Route::get('/restaurant-categorywise-sales', [ReportsController::class, 'restaurantCategorywiseSales'])->name('restaurant-categorywise-sales');
        Route::get('/cash-drawer-sessions', [ReportsController::class, 'cashDrawerSessionsReport'])->name('cash-drawer-sessions');
    });
});

require __DIR__.'/auth.php';
