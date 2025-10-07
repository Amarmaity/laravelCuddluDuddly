<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::post('admin-login', [AdminController::class, 'login'])->name('admin-login');
Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');


// Sellers
Route::prefix('admin')->group(function () {
    Route::resource('sellers', SellerController::class)->names('admin.sellers'); // index, create, store, show, edit, update, destroy
    Route::get('seller-applications', [SellerController::class, 'index'])->name('admin.seller-applications.index');
    Route::get('payouts', [PayoutController::class, 'index'])->name('admin.payouts.index');
    Route::get('sellers/compliance', [SellerController::class, 'compliance'])->name('admin.sellers.compliance');

    // Products
    Route::resource('products', ProductController::class)->names('admin.products');
    Route::resource('categories', CategoryController::class)->names('admin.categories');
    Route::post('categories/bulk-action', [CategoryController::class, 'bulkAction'])
        ->name('admin.categories.bulkAction');
    Route::post('/admin/categories/upload-image', [CategoryController::class, 'uploadImage'])->name('admin.categories.uploadImage');
    Route::resource('brands', BrandController::class)->names('admin.brands');
    Route::get('inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');

    // Orders
    Route::resource('orders', OrderController::class)->names('admin.orders');
    Route::resource('returns', ReturnController::class)->names('admin.returns');
    Route::resource('cancellations', CancellationController::class)->names('admin.cancellations');

    // Customers
    // Resource (default CRUD)
    Route::resource('customers', CustomerController::class)->names('admin.customers');
    Route::post('customers/bulk-delete', [CustomerController::class, 'bulkDelete'])
        ->name('admin.customers.bulkDelete');

    // Bulk delete (separate endpoint)
    // Route::post('customers/bulk-destroy', [CustomerController::class, 'bulkDelete'])
    //     ->name('admin.customers.bulkdelete');
    Route::patch('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])
        ->name('admin.customers.toggle-status');
    Route::resource('reviews', ReviewController::class)->names('admin.reviews');
    Route::resource('wishlists', WishlistController::class)->names('admin.wishlists');

    // Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('admin.reports.revenue');
    Route::get('reports/seller-performance', [ReportController::class, 'sellerPerformance'])->name('admin.reports.seller-performance');
    Route::get('reports/customer-insights', [ReportController::class, 'customerInsights'])->name('admin.reports.customer-insights');

    // Settings
    Route::get('settings/general', [SettingController::class, 'general'])->name('admin.settings.general');
    Route::get('settings/payments', [SettingController::class, 'payments'])->name('admin.settings.payments');
    Route::get('settings/shipping', [SettingController::class, 'shipping'])->name('admin.settings.shipping');
    Route::resource('roles', RoleController::class)->names('admin.roles');
});
