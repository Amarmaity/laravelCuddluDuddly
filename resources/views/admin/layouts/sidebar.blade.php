@php
    // Determine which groups should be open based on current route
    $openSellers =
        request()->routeIs('admin.sellers.*') ||
        request()->routeIs('admin.payouts.*') ||
        request()->routeIs('admin.seller-applications.*');

    $openProducts =
        request()->routeIs('admin.products.*') ||
        request()->routeIs('admin.categories.*') ||
        request()->routeIs('admin.brands.*') ||
        request()->routeIs('admin.inventory.*');

    $openOrders =
        request()->routeIs('admin.orders.*') ||
        request()->routeIs('admin.returns.*') ||
        request()->routeIs('admin.cancellations.*');

    $openCustomers =
        request()->routeIs('admin.customers.*') ||
        request()->routeIs('admin.reviews.*') ||
        request()->routeIs('admin.wishlists.*');

    $openWebsite =
        request()->routeIs('admin.website.*') ||
        request()->routeIs('admin.blogs.*') ||
        request()->routeIs('admin.seo.*');

    $openSupport = request()->routeIs('admin.support.*') || request()->routeIs('admin.tickets.*');

    $openReports = request()->routeIs('admin.reports.*');

    $openSettings = request()->routeIs('admin.settings.*') || request()->routeIs('admin.roles.*');
@endphp

<div class="sidebar">
    <div class="sidebar-header d-flex align-items-center p-3 border-bottom">
        <img src="{{ asset('logo/cuddlyduddly_logo.png') }}" alt="CuddlyDuddly Logo" class="me-2"
            style="height: 32px; width:auto;">
        <h4 class="m-0">CuddlyDuddly</h4>
    </div>

    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    {{-- Sellers --}}
    <a class="dropdown-toggle {{ $openSellers ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuSellers"
        role="button" aria-expanded="{{ $openSellers ? 'true' : 'false' }}" aria-controls="menuSellers">
        <i class="bi bi-shop"></i> Sellers
    </a>
    <div class="collapse ps-3 {{ $openSellers ? 'show' : '' }}" id="menuSellers" data-bs-parent=".sidebar">
        <a href="{{ route('admin.sellers.index') }}"
            class="{{ request()->routeIs('admin.sellers.index') ? 'active' : '' }}">All Sellers</a>
        <a href="{{ route('admin.seller-applications.index') }}"
            class="{{ request()->routeIs('admin.seller-applications.index') ? 'active' : '' }}">Seller Applications</a>
        <a href="{{ route('admin.sellers.compliance') }}"
            class="{{ request()->routeIs('admin.sellers.compliance') ? 'active' : '' }}">KYC / Compliance</a>
        <a href="{{ route('admin.payouts.index') }}"
            class="{{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}">Payouts</a>
    </div>

    {{-- Products --}}
    <a class="dropdown-toggle {{ $openProducts ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuProducts"
        role="button" aria-expanded="{{ $openProducts ? 'true' : 'false' }}" aria-controls="menuProducts">
        <i class="bi bi-box-seam"></i> Products
    </a>
    <div class="collapse ps-3 {{ $openProducts ? 'show' : '' }}" id="menuProducts" data-bs-parent=".sidebar">
        <a href="{{ route('admin.products.index') }}"
            class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">All Products</a>
        <a href="{{ route('admin.categories.index') }}"
            class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a>
        <a href="{{ route('admin.brands.index') }}"
            class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">Brands</a>
        <a href="{{ route('admin.inventory.index') }}"
            class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">Inventory</a>
    </div>

    {{-- Orders --}}
    <a class="dropdown-toggle {{ $openOrders ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuOrders"
        role="button" aria-expanded="{{ $openOrders ? 'true' : 'false' }}" aria-controls="menuOrders">
        <i class="bi bi-cart-check"></i> Orders
    </a>
    <div class="collapse ps-3 {{ $openOrders ? 'show' : '' }}" id="menuOrders" data-bs-parent=".sidebar">
        <a href="{{ route('admin.orders.index') }}"
            class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">All Orders</a>
        <a href="{{ route('admin.returns.index') }}"
            class="{{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">Returns</a>
        <a href="{{ route('admin.cancellations.index') }}"
            class="{{ request()->routeIs('admin.cancellations.*') ? 'active' : '' }}">Cancellations</a>
    </div>

    {{-- Customers --}}
    <a class="dropdown-toggle {{ $openCustomers ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuCustomers"
        role="button" aria-expanded="{{ $openCustomers ? 'true' : 'false' }}" aria-controls="menuCustomers">
        <i class="bi bi-people"></i> Customers
    </a>
    <div class="collapse ps-3 {{ $openCustomers ? 'show' : '' }}" id="menuCustomers" data-bs-parent=".sidebar">
        <a href="{{ route('admin.customers.index') }}"
            class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">All Customers</a>
        <a href="{{ route('admin.reviews.index') }}"
            class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">Reviews</a>
        <a href="{{ route('wishlists.index') }}"
            class="{{ request()->routeIs('admin.wishlists.*') ? 'active' : '' }}">Wishlists</a>
    </div>

    {{-- Website Content --}}
    <a class="dropdown-toggle {{ $openWebsite ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuWebsite"
        role="button" aria-expanded="{{ $openWebsite ? 'true' : 'false' }}" aria-controls="menuWebsite">
        <i class="bi bi-globe"></i> Website Content
    </a>
    <div class="collapse ps-3 {{ $openWebsite ? 'show' : '' }}" id="menuWebsite" data-bs-parent=".sidebar">
        <a href="{{ route('admin.website.banners') }}"
            class="{{ request()->routeIs('admin.website.banners') ? 'active' : '' }}">Banners</a>
        <a href="{{ route('admin.website.pages') }}"
            class="{{ request()->routeIs('admin.website.pages') ? 'active' : '' }}">Landing Pages</a>
        <a href="{{ route('admin.blogs.index') }}"
            class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">Blog Posts</a>
        <a href="{{ route('admin.seo.settings') }}"
            class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">SEO Settings</a>
    </div>

    {{-- Support --}}
    <a class="dropdown-toggle {{ $openSupport ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuSupport"
        role="button" aria-expanded="{{ $openSupport ? 'true' : 'false' }}" aria-controls="menuSupport">
        <i class="bi bi-headset"></i> Support
    </a>
    <div class="collapse ps-3 {{ $openSupport ? 'show' : '' }}" id="menuSupport" data-bs-parent=".sidebar">
        <a href="{{ route('admin.seller-supports.index') }}"
            class="{{ request()->routeIs('admin.seller-supports.index') ? 'active' : '' }}">Seller Support</a>
        <a href="{{ route('admin.support.customer') }}"
            class="{{ request()->routeIs('admin.support.customer') ? 'active' : '' }}">Customer Support</a>
        <a href="{{ route('admin.tickets.index') }}"
            class="{{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">All Tickets</a>
    </div>

    {{-- Reports --}}
    <a class="dropdown-toggle {{ $openReports ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuReports"
        role="button" aria-expanded="{{ $openReports ? 'true' : 'false' }}" aria-controls="menuReports">
        <i class="bi bi-bar-chart"></i> Reports
    </a>
    <div class="collapse ps-3 {{ $openReports ? 'show' : '' }}" id="menuReports" data-bs-parent=".sidebar">
        <a href="{{ route('admin.reports.sales') }}"
            class="{{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">Sales</a>
        <a href="{{ route('admin.reports.revenue') }}"
            class="{{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}">Revenue</a>
        <a href="{{ route('admin.reports.seller-performance') }}"
            class="{{ request()->routeIs('admin.reports.seller-performance') ? 'active' : '' }}">Seller
            Performance</a>
        <a href="{{ route('admin.reports.customer-insights') }}"
            class="{{ request()->routeIs('admin.reports.customer-insights') ? 'active' : '' }}">Customer Insights</a>
    </div>

    {{-- Settings --}}
    <a class="dropdown-toggle {{ $openSettings ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuSettings"
        role="button" aria-expanded="{{ $openSettings ? 'true' : 'false' }}" aria-controls="menuSettings">
        <i class="bi bi-gear"></i> Settings
    </a>
    <div class="collapse ps-3 {{ $openSettings ? 'show' : '' }}" id="menuSettings" data-bs-parent=".sidebar">
        <a href="{{ route('admin.settings.general') }}"
            class="{{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">General</a>
        <a href="{{ route('admin.settings.payments') }}"
            class="{{ request()->routeIs('admin.settings.payments') ? 'active' : '' }}">Payment Gateways</a>
        <a href="{{ route('admin.settings.shipping') }}"
            class="{{ request()->routeIs('admin.settings.shipping') ? 'active' : '' }}">Shipping</a>
        <a href="{{ route('admin.roles.index') }}"
            class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Roles & Permissions</a>
    </div>
</div>
