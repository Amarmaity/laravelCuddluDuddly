@extends('admin.layouts.admin')

@section('title', 'Manage Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-receipt-cutoff me-2"></i> All Orders</h4>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Order
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-center">
                <div class="col-auto flex-grow-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm" placeholder="Search by Order ID or Customer...">
                </div>

                <div class="col-auto">
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">All Payments</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="order_status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>
                            Processing</option>
                        <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Shipped
                        </option>
                        <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Delivered
                        </option>
                        <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>

                <div class="col-auto">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ request('user_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->full_name ?? 'User #' . $customer->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <select name="sort" class="form-select form-select-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="amount_high_low" {{ request('sort') == 'amount_high_low' ? 'selected' : '' }}>Amount
                            ↓</option>
                        <option value="amount_low_high" {{ request('sort') == 'amount_low_high' ? 'selected' : '' }}>Amount
                            ↑</option>
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

    <!-- Orders Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="order-row" data-id="{{ $order->id }}" style="cursor: pointer;">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->user->full_name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ ucfirst($order->order_status) }}</span>
                            </td>
                            <td>{{ $order->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders && $orders->hasPages())
            <div class="card-footer">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
    <!-- Modal Container -->
    <div class="modal fade" id="orderQuickViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0" id="orderQuickViewContent">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 mb-0">Loading order details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" data-store-url="{{ route('admin.cancellations.store') }}"> tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title">
                        <i class="bi bi-x-octagon-fill me-1"></i> Cancel Order
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="cancelOrderForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-2">
                            Please select a reason for cancellation:
                        </p>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason1"
                                value="Ordered by mistake">
                            <label class="form-check-label" for="reason1">Ordered by mistake</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason2"
                                value="Found a better price elsewhere">
                            <label class="form-check-label" for="reason2">Found a better price elsewhere</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reason3"
                                value="Product no longer needed">
                            <label class="form-check-label" for="reason3">Product no longer needed</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="reason" id="reasonOther"
                                value="other">
                            <label class="form-check-label" for="reasonOther">Other</label>
                        </div>

                        <textarea id="reasonText" class="form-control d-none mt-2" rows="3" placeholder="Write your reason..."></textarea>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-check-circle"></i> Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ asset('js/order-index.js') }}"></script>
    @endpush
@endsection
