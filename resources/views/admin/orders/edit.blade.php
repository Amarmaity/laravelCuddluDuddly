@extends('admin.layouts.admin')

@section('title', 'Edit Order')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-pencil-square me-2"></i> Edit Order #{{ $order->id }}</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
    </div>

    <form id="orderEditForm" action="{{ route('admin.orders.update', $order->id) }}" method="POST"
        class="card shadow-sm border-0">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row g-4">
                <!-- Customer -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Customer</label>
                    <select name="user_id" class="form-select" id="customerSelect" required>
                        <option value="">-- Choose Customer --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $order->user_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->full_name }} ({{ $customer->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="cod" {{ $order->payment_method == 'cod' ? 'selected' : '' }}>Cash on Delivery
                        </option>
                        <option value="online" {{ $order->payment_method == 'online' ? 'selected' : '' }}>Online Payment
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Payment Status</label>
                    <select name="payment_status" class="form-select">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <!-- Shipping Details -->
                <div class="col-12">
                    <h6 class="fw-bold border-bottom pb-2 mt-3 text-primary">
                        <i class="bi bi-geo-alt-fill me-2"></i> Shipping Details
                    </h6>

                    <div id="existing-addresses" class="mt-3">
                        <div class="text-muted small">Select a customer to view saved addresses...</div>
                    </div>

                    <div class="mt-3">
                        <button type="button" id="toggle-new-address" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Add New Address
                        </button>
                    </div>

                    <div id="new-address-form" class="border rounded-3 p-3 mt-3 bg-light shadow-sm" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Recipient Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="shipping_name" value="{{ old('shipping_name') }}"
                                    class="form-control shadow-sm @error('shipping_name') is-invalid @enderror"
                                    placeholder="Full name">
                                @error('shipping_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone') }}"
                                    class="form-control shadow-sm @error('shipping_phone') is-invalid @enderror"
                                    placeholder="Phone number">
                                @error('shipping_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="shipping_email" value="{{ old('shipping_email') }}"
                                    class="form-control shadow-sm @error('shipping_email') is-invalid @enderror"
                                    placeholder="Optional email">
                                @error('shipping_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Landmark</label>
                                <input type="text" name="landmark" value="{{ old('landmark') }}"
                                    class="form-control shadow-sm @error('landmark') is-invalid @enderror"
                                    placeholder="Nearby landmark">
                                @error('landmark')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address Line 1 <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="address_line1" value="{{ old('address_line1') }}"
                                    class="form-control shadow-sm @error('address_line1') is-invalid @enderror"
                                    placeholder="House No, Street">
                                @error('address_line1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address Line 2</label>
                                <input type="text" name="address_line2" value="{{ old('address_line2') }}"
                                    class="form-control shadow-sm @error('address_line2') is-invalid @enderror"
                                    placeholder="Apartment, Building">
                                @error('address_line2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                    class="form-control shadow-sm @error('city') is-invalid @enderror">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" value="{{ old('state') }}"
                                    class="form-control shadow-sm @error('state') is-invalid @enderror">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Postal Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                    class="form-control shadow-sm @error('postal_code') is-invalid @enderror">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="col-12">
                    <h6 class="fw-bold border-bottom pb-2 mt-4 text-primary">
                        <i class="bi bi-cart4 me-2"></i> Order Items
                    </h6>

                    <div class="table-responsive mt-3">
                        <table class="table align-middle mb-0" id="order-items-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 55%;">Product</th>
                                    <th style="width: 25%;">Quantity</th>
                                    <th style="width: 20%;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="order-items-body">
                                @foreach ($order->items as $index => $item)
                                    <tr class="order-item">
                                        <td>
                                            <select name="product_id[]" class="form-select form-select-sm shadow-sm"
                                                required>
                                                <option value="">-- Select Product --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="quantity[]"
                                                class="form-control form-control-sm shadow-sm" min="1"
                                                value="{{ $item->quantity }}" required>
                                        </td>
                                        <td class="text-center">
                                            @if ($loop->first)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success add-item rounded-circle">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger remove-item rounded-circle">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                <div class="col-12 mt-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-journal-text me-1 text-primary"></i> Order Notes
                    </label>
                    <textarea name="notes" class="form-control shadow-sm" rows="3">{{ $order->notes }}</textarea>
                </div>

                <!-- Order Status -->
                <div class="col-md-4 mt-4">
                    <label class="form-label fw-semibold">Order Status</label>
                    <select name="order_status" class="form-select" required>
                        <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing
                        </option>
                        <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered
                        </option>
                        <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-light">Cancel</a>
            <button type="submit" id="updateOrderBtn" class="btn btn-success">
                <i class="bi bi-check-lg"></i> Update Order
            </button>
        </div>
    </form>
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title" id="editAddressModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> Edit Shipping Address
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-address-form">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="id" id="edit_address_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Recipient Name</label>
                                <input type="text" class="form-control" name="shipping_name" id="edit_shipping_name"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" class="form-control" name="shipping_phone"
                                    id="edit_shipping_phone" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" name="shipping_email"
                                    id="edit_shipping_email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Landmark</label>
                                <input type="text" class="form-control" name="landmark" id="edit_landmark">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address Line 1</label>
                                <input type="text" class="form-control" name="address_line1" id="edit_address_line1"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address Line 2</label>
                                <input type="text" class="form-control" name="address_line2" id="edit_address_line2">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" class="form-control" name="city" id="edit_city" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text" class="form-control" name="state" id="edit_state" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Postal Code</label>
                                <input type="text" class="form-control" name="postal_code" id="edit_postal_code"
                                    required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="save-address-changes" class="btn btn-success">
                        <i class="bi bi-check2"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.orderData = {
            orderId: "{{ $order->id }}",
            shippingAddressId: "{{ $order->shipping_address_id }}",
            userId: "{{ $order->user_id }}"
        };
        const selectedAddressId = window.orderData.shippingAddressId;
        const currentUserId = window.orderData.userId;
    </script>

    @push('scripts')
        <script src="{{ asset('js/order-edit.js') }}"></script>
    @endpush
@endsection
