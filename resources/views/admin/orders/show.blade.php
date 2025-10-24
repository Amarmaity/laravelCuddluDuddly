@extends('admin.layouts.admin')

@section('title', 'Order Details')

@section('content')
    <div class="container-fluid mt-4">

        {{-- Header + Actions --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff me-1"></i> Order #{{ $order->order_number }}</h5>
                <small class="text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <a href="#" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-printer me-1"></i> Print Invoice
                </a>
                <a href="#" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-envelope me-1"></i> Send Mail
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="card border-0 shadow-sm p-3 mb-3">
            <div class="row gx-3 gy-2 small">
                <div class="col-md-3">
                    <div class="border-start border-4 border-primary ps-2">
                        <div class="text-muted fw-semibold">Customer</div>
                        <div>{{ $order->user?->full_name ?? 'N/A' }}</div>
                        <small class="text-muted"><i class="bi bi-envelope"></i> {{ $order->user?->email }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-start border-4 border-info ps-2">
                        <div class="text-muted fw-semibold">Payment</div>
                        <div>{{ ucfirst($order->payment_method ?? 'N/A') }}</div>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-start border-4 border-warning ps-2">
                        <div class="text-muted fw-semibold">Status</div>
                        <span
                            class="badge bg-{{ match ($order->order_status) {
                                'delivered' => 'success',
                                'shipped' => 'info',
                                'processing' => 'warning',
                                'cancelled' => 'danger',
                                default => 'secondary',
                            } }} px-3 py-1">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-start border-4 border-success ps-2">
                        <div class="text-muted fw-semibold">Total</div>
                        <div class="fs-6 fw-bold text-success">₹{{ number_format($order->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>
            @if ($order->notes)
                <div class="mt-2 small text-muted">
                    <i class="bi bi-chat-dots me-1"></i>
                    <strong>Notes:</strong> <em>{{ $order->notes }}</em>
                </div>
            @endif
        </div>

        {{-- Shipping + Items --}}
        <div class="row g-3">
            {{-- Shipping --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header py-2 bg-light fw-semibold small">
                        <i class="bi bi-geo-alt me-1"></i> Shipping Address
                    </div>
                    <div class="card-body small py-2">
                        @if ($order->shippingAddress)
                            <div class="border-start border-3 border-secondary ps-2">
                                <strong>{{ $order->shippingAddress->shipping_name }}</strong><br>
                                <i class="bi bi-telephone text-muted"></i>
                                {{ $order->shippingAddress->shipping_phone }}<br>
                                <i class="bi bi-envelope text-muted"></i> {{ $order->shippingAddress->shipping_email }}<br>
                                <div class="text-muted">
                                    {{ $order->shippingAddress->address_line1 }},
                                    @if ($order->shippingAddress->address_line2)
                                        {{ $order->shippingAddress->address_line2 }},
                                    @endif
                                    {{ $order->shippingAddress->city }},
                                    {{ $order->shippingAddress->state }} -
                                    {{ $order->shippingAddress->postal_code }}<br>
                                    {{ $order->shippingAddress->country }}
                                </div>
                                @if ($order->shippingAddress->landmark)
                                    <small class="text-muted"><i class="bi bi-geo me-1"></i> Landmark:
                                        {{ $order->shippingAddress->landmark }}</small><br>
                                @endif
                                {{-- @if ($order->shippingAddress->is_default)
                                    <span class="badge bg-success mt-1"><i class="bi bi-check-circle"></i> Default</span>
                                @endif --}}
                            </div>
                        @else
                            <p class="text-muted fst-italic mb-0">No shipping address found.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header py-2 bg-light fw-semibold small d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bag-check me-1"></i> Order Items</span>
                        <div class="small text-muted">Total Items: {{ $order->items->count() }}</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light text-center small">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start">Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @forelse($order->items as $index => $item)
                                        <tr>
                                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                                            <td class="text-start">
                                                <div class="fw-semibold">{{ $item->product?->name ?? 'Unknown' }}</div>
                                                <small class="text-muted">#{{ $item->product_id }}</small>
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-center">₹{{ number_format($item->price, 2) }}</td>
                                            <td class="text-center fw-semibold text-success">
                                                ₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">No items found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($order->items->count())
                                    <tfoot class="table-light small">
                                        <tr>
                                            <td colspan="4" class="text-end fw-semibold">Grand Total</td>
                                            <td class="text-center text-success fw-bold">
                                                ₹{{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
