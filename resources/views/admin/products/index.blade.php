@extends('admin.layouts.admin')

@section('title', 'Manage Products')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-shop me-2"></i> All Products</h4>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-center">
                <div class="col-auto flex-grow-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm" placeholder="Search product...">
                </div>

                <div class="col-auto">
                    <select name="approval_status" class="form-select form-select-sm">
                        <option value="">All Approval</option>
                        <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="featured" class="form-select form-select-sm">
                        <option value="">All Featured</option>
                        <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="seller_id" class="form-select form-select-sm">
                        <option value="">All Sellers</option>
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                {{ $seller->name ?? 'Seller #' . $seller->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <select name="sort" class="form-select form-select-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price ↑
                        </option>
                        <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price ↓
                        </option>
                        <option value="stock_low_high" {{ request('sort') == 'stock_low_high' ? 'selected' : '' }}>Stock ↑
                        </option>
                        <option value="stock_high_low" {{ request('sort') == 'stock_high_low' ? 'selected' : '' }}>Stock ↓
                        </option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A–Z)</option>
                    </select>
                </div>

                <div class="col-auto d-grid">
                    <button type="submit" class="btn btn-dark btn-sm">
                        <i class="bi bi-funnel me-1"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-3 d-flex gap-2">
        <button class="btn btn-danger btn-sm" id="deleteSelected">
            <i class="bi bi-trash"></i> Delete Selected
        </button>
        <button class="btn btn-warning btn-sm" id="featureSelected">
            <i class="bi bi-star"></i> Feature Selected
        </button>
        <button class="btn btn-success btn-sm" id="approveSelected">
            <i class="bi bi-check2-circle"></i> Approve Selected
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>#</th>
                            <th>Product</th>
                            <th>Seller</th>
                            <th style="min-width: 220px;">Category Chain</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Approval</th>
                            <th class="text-end">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($products && $products->count() > 0)
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $product->name }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                    </td>
                                    <td>{{ $product->seller->name ?? '—' }}</td>
                                    <td style="max-width: 350px; white-space: normal; word-wrap: break-word;">
                                        @php
                                            $chains = $product->categorySections
                                                ->map(function ($item) {
                                                    return ($item->masterCategory->name ?? 'N/A') .
                                                        ' → ' .
                                                        ($item->sectionType->name ?? 'N/A') .
                                                        ' → ' .
                                                        ($item->category->name ?? 'N/A');
                                                })
                                                ->implode(', ');
                                        @endphp

                                        <small class="text-muted" title="{{ $chains }}">
                                            {{ Str::limit($chains, 80) }}
                                        </small>
                                    </td>

                                    <td>₹{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($product->featured)
                                            <span class="badge bg-warning text-dark">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->is_approved)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item btn-view-product"
                                                        data-id="{{ $product->id }}">
                                                        <i class="bi bi-eye me-1"></i> View
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.products.edit', $product->id) }}">
                                                        <i class="bi bi-pencil me-1"></i> Edit
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="bi bi-box-seam fs-4 d-block mb-1"></i>
                                    No products found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if ($products && $products->hasPages())
            <div class="card-footer">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ asset('js/products-index.js') }}"></script>
    @endpush
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to permanently delete the selected products?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Quick View Modal -->
    <div class="modal fade" id="productViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Product Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="productViewContent">
                    <div class="text-center text-muted py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2 mb-0 small">Loading product details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
