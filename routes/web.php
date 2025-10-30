<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AdminController,
    DashboardController,
    CategoryController,
    CustomerController,
    SellerController,
    PayoutController,
    WebhookController,
    ProductController,
    BrandController,
    InventoryController,
    ReviewController,
    OrderController,
    ReturnController,
    CancellationController,
    ReportController,
    SettingsController,
    RoleController,
    WebsiteController,
    BlogController,
    SellerSupportController,
    SEOController,
    SupportController,
    TicketController,
    WishlistController,
};

// Public welcome
Route::get('/', fn() => view('welcome'));

// Admin login & logout
Route::get('/admin', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin-login', [AdminController::class, 'login'])->name('admin-login');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Protected admin routes (session check applied here)
Route::prefix('admin')->middleware('admin.auth')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Sellers
    Route::resource('sellers', SellerController::class)->names('admin.sellers');
    Route::get('all-sellers', [SellerController::class, 'AllSellers'])->name('admin.allsellers.all-sellers');
    Route::get('seller-applications', [SellerController::class, 'create'])->name('admin.seller-applications.index');
    Route::get('seller-applications/{seller}/download-docs', [SellerController::class, 'viewDocs'])->name('admin.seller-applications.viewDocs');
    Route::get('seller-compliance', [SellerController::class, 'compliance'])->name('admin.sellers.compliance');
    Route::patch('seller-compliance/{seller}/accept', [SellerController::class, 'KYCaccept'])->name('admin.sellers.compliance.accept');
    Route::patch('seller-compliance/{seller}/reject', [SellerController::class, 'KYCreject'])->name('admin.sellers.compliance.reject');
    Route::get('sellers/bank-details/{seller}', [SellerController::class, 'bankDetails'])->name('admin.sellers.bankDetails');

    // Payouts & Webhooks
    Route::resource('payouts', PayoutController::class)->names('admin.payouts');
    Route::post('webhooks/razorpayx', [WebhookController::class, 'razorpayx'])->name('admin.webhooks.razorpayx');

    // Products & Categories
    Route::resource('products', ProductController::class)->names('admin.products');
    Route::resource('categories', CategoryController::class)->names('admin.categories');
    Route::post('categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('admin.categories.bulkAction');
    Route::post('categories/upload-image', [CategoryController::class, 'uploadImage'])->name('admin.categories.uploadImage');
    Route::post('products/bulk-feature', [ProductController::class, 'bulkFeature']);
    Route::post('products/bulk-approve', [ProductController::class, 'bulkApprove']);
    Route::get('products/{id}/quick-view', [ProductController::class, 'quickView'])->name('admin.products.quickView');

    // brands & inventory
    Route::resource('brands', BrandController::class)->names('admin.brands');
    Route::get('inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');

    // Orders
    Route::resource('orders', OrderController::class)->names('admin.orders');
    Route::get('orders/{id}/quick-view', [OrderController::class, 'quickView'])->name('admin.orders.quickView');
    Route::get('orders/get-addresses/{user}', [OrderController::class, 'getAddresses'])->name('admin.orders.get-addresses');
    Route::get('shipping-addresses/{id}', [OrderController::class, 'ShippingAddressshow']);
    Route::post('shipping-addresses/{id}', [OrderController::class, 'ShippingAddressupdate']);
    Route::delete('shipping-addresses/{id}', [OrderController::class, 'ShippingAddressdestroy']);

    // Returns & Cancellations
    Route::resource('returns', ReturnController::class)->names('admin.returns');
    Route::resource('cancellations', CancellationController::class)->names('admin.cancellations');
    Route::patch('cancellations/{id}/approve', [CancellationController::class, 'approve'])->name('admin.cancellations.approve');
    Route::patch('cancellations/{id}/reject', [CancellationController::class, 'reject'])->name('admin.cancellations.reject');

    // Customers
    Route::resource('customers', CustomerController::class)->names('admin.customers');
    Route::post('customers/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('admin.customers.bulkDelete');
    Route::patch('{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('admin.customers.toggle-status');

    // Reviews, Wishlists, Reports
    Route::resource('reviews', ReviewController::class)->names('admin.reviews');
    Route::resource('wishlists', WishlistController::class)->names('wishlists');
    Route::post('wishlist/bulk-delete', [WishlistController::class, 'bulkDelete'])->name('wishlist.bulk-delete');

    Route::get('reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('admin.reports.revenue');
    Route::get('reports/seller-performance', [ReportController::class, 'sellerPerformance'])->name('admin.reports.seller-performance');
    Route::get('reports/customer-insights', [ReportController::class, 'customerInsights'])->name('admin.reports.customer-insights');

    // Settings & Roles
    Route::get('settings/general', [SettingsController::class, 'general'])->name('admin.settings.general');
    Route::post('settings/update', [SettingsController::class, 'update'])->name('admin.settings.general.update');

    Route::get('settings/payments', [SettingController::class, 'payments'])->name('admin.settings.payments');
    Route::get('settings/shipping', [SettingController::class, 'shipping'])->name('admin.settings.shipping');
    Route::resource('roles', RoleController::class)->names('admin.roles');

    // Website CMS
    Route::get('website/banners', [WebsiteController::class, 'banners'])->name('admin.website.banners');
    Route::get('website/pages', [WebsiteController::class, 'pages'])->name('admin.website.pages');
    Route::resource('blogs', BlogController::class)->names('admin.blogs');

    // SEO
    Route::get('seo/settings', [SEOController::class, 'index'])->name('admin.seo.settings');
    Route::post('seo/settings/update', [SEOController::class, 'update'])->name('admin.seo.update');

    // Support & Tickets
    Route::get('support/seller', [SupportController::class, 'seller'])->name('admin.support.seller');
    Route::resource('seller-supports', SellerSupportController::class)->names('admin.seller-supports');
    Route::get('products/{productId}/reviews', [SellerSupportController::class, 'searchReview']);

    Route::get('/seller-supports/{id}/messages', [SellerSupportController::class, 'getMessages'])->name('seller-supports.messages');
    Route::post('/seller-supports/{id}/messages', [SellerSupportController::class, 'storeMessage'])->name('seller-supports.messages.store');
    Route::post('seller-supports/{id}/update-status', [SellerSupportController::class, 'updateStatus'])->name('seller-supports.update-status');
    Route::put('/sellers/{seller}/bank-info', [SellerSupportController::class, 'updateBankInfo'])->name('sellers.updateBankInfo');

    Route::get('support/customer', [SupportController::class, 'customer'])->name('admin.support.customer');
    Route::resource('tickets', TicketController::class)->names('admin.tickets');
});
