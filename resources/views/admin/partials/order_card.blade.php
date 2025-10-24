{{-- UI-1 --}}

{{-- <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-receipt me-2"></i> Order #{{ $order->order_number }}
        </h6>
        <span class="badge bg-light text-dark text-capitalize">{{ $order->status }}</span>
    </div>

    <div class="card-body p-3">
        <div class="row g-3">

            <!-- Customer -->
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1">
                    <i class="bi bi-person-circle me-2"></i>Customer
                </h6>
                <p class="mb-1"><strong>{{ $order->user->full_name }}</strong></p>
                <p class="mb-0 small text-muted">{{ $order->user->email }}</p>
                <p class="mb-0 small text-muted">{{ $order->user->phone }}</p>
            </div>

            <!-- Shipping -->
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1">
                    <i class="bi bi-geo-alt me-2"></i>Shipping
                </h6>
                <p class="mb-1 small">
                    {{ $order->shippingAddress->shipping_name }}<br>
                    {{ $order->shippingAddress->address_line1 }}<br>
                    {{ $order->shippingAddress->address_line2 ? $order->shippingAddress->address_line2 . ',' : '' }}
                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} -
                    {{ $order->shippingAddress->postal_code }}<br>
                    {{ $order->shippingAddress->country }}<br>
                    <i class="bi bi-telephone"></i> {{ $order->shippingAddress->phone }}
                </p>
            </div>

            <!-- Order Items -->
            <div class="col-12">
                <h6 class="fw-bold border-bottom pb-2 mt-3">
                    <i class="bi bi-bag-check me-2"></i> Items
                </h6>
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th width="80">Qty</th>
                                <th width="100">Price</th>
                                <th width="100">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₹{{ number_format($item->price, 2) }}</td>
                                    <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment & Total -->
            <div class="col-md-6 mt-3">
                <h6 class="fw-bold border-bottom pb-1">
                    <i class="bi bi-wallet2 me-2"></i> Payment
                </h6>
                <p class="small mb-1">
                    Method: <strong class="text-capitalize">{{ $order->payment_method }}</strong><br>
                    Status: <span
                        class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
            </div>

            <div class="col-md-6 mt-3 text-end">
                <h5 class="fw-bold text-primary">
                    Total: ₹{{ number_format($order->total_amount, 2) }}
                </h5>
            </div>

        </div>
    </div>

    <div class="card-footer bg-light text-end">
        <small class="text-muted">Ordered at: {{ $order->created_at->format('d M Y, h:i A') }}</small>
    </div>
</div> --}}



{{-- UI-2 --}}



{{-- <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

    <!-- Header -->
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-receipt me-2"></i> Order #{{ $order->order_number }}
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <!-- Body -->
    <div class="card-body p-4">
        <div class="row g-3">

            <!-- Customer -->
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1 text-secondary">
                    <i class="bi bi-person-circle me-2"></i>Customer
                </h6>
                <p class="mb-1"><strong>{{ $order->user->full_name }}</strong></p>
                <p class="mb-0 small text-muted">{{ $order->user->email }}</p>
                <p class="mb-0 small text-muted">{{ $order->user->phone }}</p>
            </div>

            <!-- Shipping -->
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-1 text-secondary">
                    <i class="bi bi-geo-alt me-2"></i>Shipping Address
                </h6>
                <div class="small">
                    <strong>{{ $order->shippingAddress->shipping_name }}</strong><br>
                    {{ $order->shippingAddress->address_line1 }}<br>
                    {{ $order->shippingAddress->address_line2 ? $order->shippingAddress->address_line2 . ',' : '' }}
                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} -
                    {{ $order->shippingAddress->postal_code }}<br>
                    {{ $order->shippingAddress->country }}<br>
                    @if ($order->shippingAddress->landmark)
                        <span class="text-muted">Landmark: {{ $order->shippingAddress->landmark }}</span><br>
                    @endif
                    <i class="bi bi-telephone"></i> {{ $order->shippingAddress->shipping_phone }}
                </div>
            </div>

            <!-- Order Items -->
            <div class="col-12">
                <h6 class="fw-bold border-bottom pb-2 mt-3 text-secondary">
                    <i class="bi bi-bag-check me-2"></i> Items
                </h6>
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th width="80">Qty</th>
                                <th width="100">Price</th>
                                <th width="100">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₹{{ number_format($item->price, 2) }}</td>
                                    <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment & Total -->
            <div class="col-md-6 mt-3">
                <h6 class="fw-bold border-bottom pb-1 text-secondary">
                    <i class="bi bi-wallet2 me-2"></i> Payment
                </h6>
                <p class="small mb-1">
                    Method: <strong class="text-capitalize">{{ $order->payment_method }}</strong><br>
                    Status: <span
                        class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
            </div>

            <!-- Total -->
            <div class="col-md-6 mt-3 text-end">
                <h5 class="fw-bold text-primary mb-0">
                    Total: ₹{{ number_format($order->total_amount, 2) }}
                </h5>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="card-footer bg-light d-flex flex-wrap justify-content-between align-items-center py-3 px-4">
        <small class="text-muted">
            Ordered at: {{ $order->created_at->format('d M Y, h:i A') }}
        </small>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-sm btn-success">
                <i class="bi bi-truck"></i> Mark as Shipped
            </button>
            <button class="btn btn-sm btn-warning">
                <i class="bi bi-arrow-repeat"></i> Refund
            </button>
            <button class="btn btn-sm btn-danger">
                <i class="bi bi-x-circle"></i> Cancel Order
            </button>
            <button class="btn btn-sm btn-outline-primary">
                <i class="bi bi-printer"></i> Print Invoice
            </button>
        </div>
    </div>
</div> --}}




{{-- UI-3 --}}



{{-- <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

        <!-- Header -->
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">
                <i class="bi bi-receipt me-2"></i> Order #{{ $order->order_number }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Body -->
        <div class="modal-body bg-light">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4">

                        <!-- Customer -->
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 text-primary">
                                <i class="bi bi-person-circle me-2"></i> Customer Details
                            </h6>
                            <p class="mb-1"><strong>{{ $order->user->full_name }}</strong></p>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-envelope me-1"></i>{{ $order->user->email }}
                            </p>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-telephone me-1"></i>{{ $order->user->phone }}
                            </p>
                        </div>

                        <!-- Shipping -->
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 text-primary">
                                <i class="bi bi-geo-alt-fill me-2"></i> Shipping Address
                            </h6>
                            <div class="small">
                                <div><strong>{{ $order->shippingAddress->shipping_name }}</strong></div>
                                @if ($order->shippingAddress->shipping_email)
                                    <div class="text-muted">
                                        <i class="bi bi-envelope me-1"></i>{{ $order->shippingAddress->shipping_email }}
                                    </div>
                                @endif
                                <div>
                                    <i class="bi bi-telephone me-1"></i>{{ $order->shippingAddress->shipping_phone }}
                                </div>
                                <div class="mt-1">{{ $order->shippingAddress->address_line1 }}</div>
                                @if ($order->shippingAddress->address_line2)
                                    <div>{{ $order->shippingAddress->address_line2 }}</div>
                                @endif
                                @if ($order->shippingAddress->landmark)
                                    <div class="text-muted small">
                                        <i class="bi bi-geo me-1"></i> Landmark: {{ $order->shippingAddress->landmark }}
                                    </div>
                                @endif
                                <div>
                                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} -
                                    {{ $order->shippingAddress->postal_code }}
                                </div>
                                <div class="text-muted">{{ $order->shippingAddress->country }}</div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold border-bottom pb-2 text-primary">
                                <i class="bi bi-bag-check-fill me-2"></i> Ordered Items
                            </h6>
                            <div class="table-responsive mt-2">
                                <table class="table table-sm align-middle table-striped border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th width="80">Qty</th>
                                            <th width="100">Price</th>
                                            <th width="100">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="col-md-6 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 text-primary">
                                <i class="bi bi-wallet2 me-2"></i> Payment Information
                            </h6>
                            <p class="small mb-1">
                                Method: <strong class="text-capitalize">{{ $order->payment_method }}</strong><br>
                                Status:
                                <span
                                    class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                        </div>

                        <!-- Total -->
                        <div class="col-md-6 mt-4 text-end">
                            <h4 class="fw-bold text-success mb-0">
                                Total: ₹{{ number_format($order->total_amount, 2) }}
                            </h4>
                            <small class="text-muted d-block mt-1">
                                Ordered on {{ $order->created_at->format('d M Y, h:i A') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer / Actions -->
        <div class="modal-footer bg-white border-top d-flex justify-content-between align-items-center">
            <div class="text-start">
                <span class="badge bg-light text-dark text-capitalize px-3 py-2 border">
                    Status: {{ ucfirst($order->order_status) }}
                </span>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Close
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil-square"></i> Edit Order
                </button>
                <button type="button" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-truck"></i> Mark as Shipped
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-arrow-repeat"></i> Refund
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div> --}}



{{-- UI-4 --}}


<style>
    .modal__btn-group .btn.btn-sm i {
        font-size: 1rem;
        vertical-align: middle;
    }

    .modal__btn-group .btn.btn-sm {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
</style>

<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable m-0">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
        <!-- Header -->
        <div class="modal-header bg-secondary text-white d-flex justify-content-between align-items-center gap-2">
            <h5 class="modal-title mb-0">
                <i class="bi bi-receipt me-2"></i> Order #{{ $order->order_number }}
            </h5>
            <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill shadow-sm border">
                <i class="bi bi-circle-fill small me-1"></i> Order Status:
                <span class="text-uppercase">{{ ucfirst($order->order_status) }}</span>
            </span>
            {{-- <button type="button" class="btn-close btn-close-white position-absolute right-0" data-bs-dismiss="modal" aria-label="Close"></button> --}}
        </div>

        <!-- Body -->
        <div class="modal-body bg-light py-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">

                    <div class="row g-3">
                        <!-- Items -->
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 text-primary small mb-2">
                                <i class="bi bi-bag-check-fill me-2"></i> Ordered Items
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle table-striped border mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th width="80">Qty</th>
                                            <th width="100">Price</th>
                                            <th width="100">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="col-md-6">
                            <div class="border-end h-100">
                                <h6 class="fw-bold pb-2 text-primary small mb-2">
                                    <i class="bi bi-wallet2 me-2"></i> Payment Information
                                </h6>
                                <div class="small mb-1">
                                    <p class="mb-1">Method: <strong
                                            class="text-capitalize">{{ $order->payment_method }}</strong></p>
                                    <p class="m-0">Status:
                                        <span
                                            class="badge rounded-pill px-3 py-1 text-capitalize
                                            bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning text-dark' : 'danger') }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="col-md-6 text-end">
                            <div class="d-flex justify-content-between gap-3">
                                <p class="text-black m-0">Subtotal:</p>
                                <p class="text-black mb-0">
                                    ₹{{ number_format($order->total_amount, 2) }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between gap-3">
                                <p class="text-black m-0">Shipping charge:</p>
                                <p class="text-black mb-0">
                                    ₹0.00
                                </p>
                            </div>
                            <div class="d-flex justify-content-between gap-3">
                                <p class="text-black m-0">Others charges:</p>
                                <p class="text-black mb-0">
                                    ₹0.00
                                </p>
                            </div>
                            <div class="d-flex justify-content-between gap-3">
                                <p class="fw-bold text-black m-0">Grand total:</p>
                                <h6 class="fw-bold text-success mb-0">
                                    ₹{{ number_format($order->total_amount, 2) }}
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">

                        <!-- Customer -->
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 text-primary small mb-2">
                                <i class="bi bi-person-circle me-2"></i> Customer Details
                            </h6>
                            <p class="mb-1"><strong>{{ $order->user->full_name }}</strong></p>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-envelope me-1"></i>{{ $order->user->email }}
                            </p>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-telephone me-1"></i>{{ $order->user->phone }}
                            </p>
                        </div>

                        <!-- Shipping -->
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 text-primary small mb-2">
                                <i class="bi bi-geo-alt-fill me-2"></i> Shipping Address
                            </h6>
                            <div class="small">
                                <div><strong>{{ $order->shippingAddress->shipping_name }}</strong></div>
                                @if ($order->shippingAddress->shipping_email)
                                    <div class="text-muted">
                                        <i
                                            class="bi bi-envelope me-1"></i>{{ $order->shippingAddress->shipping_email }}
                                    </div>
                                @endif
                                <div>
                                    <i class="bi bi-telephone me-1"></i>{{ $order->shippingAddress->shipping_phone }}
                                </div>
                                <div class="mt-1">{{ $order->shippingAddress->address_line1 }}</div>
                                @if ($order->shippingAddress->address_line2)
                                    <div>{{ $order->shippingAddress->address_line2 }}</div>
                                @endif
                                <div>
                                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} -
                                    {{ $order->shippingAddress->postal_code }}
                                </div>
                                <div class="text-muted">{{ $order->shippingAddress->country }}</div>
                                @if ($order->shippingAddress->landmark)
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-geo me-1"></i> Landmark:
                                        {{ $order->shippingAddress->landmark }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer / Actions -->
        <div class="modal-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3">
            <div class="text-start">
                <small class="text-muted d-block">
                    Ordered on: {{ $order->created_at->format('d M Y, h:i A') }}
                </small>
            </div>

            <div class="d-flex gap-2 flex-wrap justify-content-end modal__btn-group">
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-outline-primary btn-sm"
                    title="Edit Order">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <button type="button" class="btn btn-outline-success btn-sm" title="Mark as Shipped">
                    <i class="bi bi-truck"></i>
                </button>
                <button type="button" class="btn btn-outline-info btn-sm" title="Refund">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm cancel-order-btn"
                    data-order-id="{{ $order->id }}" title="Cancel Order">
                    <i class="bi bi-x-octagon"></i>
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm" title="Print Invoice">
                    <i class="bi bi-printer"></i>
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" title="Delete Order">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>
