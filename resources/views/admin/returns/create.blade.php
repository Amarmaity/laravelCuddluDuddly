@extends('admin.layouts.admin')

@section('title', 'Create Return')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-arrow-return-left me-1"></i> New Return
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.returns.store') }}" method="POST" id="returnCreateForm">
                @csrf

                <div class="row g-3">

                    {{-- ORDER SELECT (contains data-* for JS) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Order</label>
                        <select name="order_id" id="selectOrder" class="form-select" required>
                            <option value="">Select an order — starts with recent</option>
                            @foreach ($orders as $ord)
                                {{-- encode items as JSON so JS can populate item choices --}}
                                @php
                                    $itemsPayload = $ord->items
                                        ->map(function ($it) {
                                            return [
                                                'id' => $it->id,
                                                'product_name' => $it->product->name ?? 'N/A',
                                                'qty' => $it->quantity,
                                                'price' => $it->price,
                                            ];
                                        })
                                        ->toArray();
                                @endphp
                                <option value="{{ $ord->id }}" data-user-id="{{ $ord->user_id }}"
                                    data-customer-name="{{ $ord->user->full_name ?? ($ord->user->name ?? 'N/A') }}"
                                    data-customer-email="{{ $ord->user->email ?? '' }}"
                                    data-order-number="{{ $ord->order_number }}" data-items='@json($itemsPayload)'>
                                    {{ $ord->order_number }} — ₹{{ number_format($ord->total_amount, 2) }} —
                                    {{ $ord->created_at->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CUSTOMER PREVIEW (read-only, for clarity) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer</label>
                        <div class="border rounded p-2 bg-light">
                            <div id="customerPreview" class="small text-muted">
                                <div><strong id="custName">—</strong></div>
                                <div id="custEmail">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- HIDDEN user_id (populated automatically) --}}
                    <input type="hidden" name="user_id" id="hiddenUserId" value="">

                    {{-- ORDER ITEM SELECT (which item from the order is being returned) --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Order Item</label>
                        <select name="order_item_id" id="selectOrderItem" class="form-select" required>
                            <option value="">Select order → pick item</option>
                            {{-- populated by JS --}}
                        </select>
                    </div>

                    {{-- REFUND METHOD --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Refund Method</label>
                        <select name="refund_method" class="form-select" required>
                            <option value="" selected disabled>Select</option>
                            <option value="original">Original Payment</option>
                            <option value="wallet">Wallet</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>

                    {{-- REASON --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Reason for Return</label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="Describe why the customer wants to return"
                            required>{{ old('reason') }}</textarea>
                    </div>

                    {{-- REFUND AMOUNT --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Refund Amount (₹)</label>
                        <input type="number" name="refund_amount" step="0.01" class="form-control"
                            placeholder="Leave empty to auto-calc">
                        <div class="form-text small">If left empty, admin will set the amount on review.</div>
                    </div>

                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Save Return
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectOrder = document.getElementById('selectOrder');
                const custName = document.getElementById('custName');
                const custEmail = document.getElementById('custEmail');
                const hiddenUserId = document.getElementById('hiddenUserId');
                const selectOrderItem = document.getElementById('selectOrderItem');

                function resetCustomerPreview() {
                    custName.textContent = '—';
                    custEmail.textContent = '';
                    hiddenUserId.value = '';
                }

                function populateItems(items) {
                    selectOrderItem.innerHTML = '<option value="">Select order → pick item</option>';
                    if (!items || !items.length) return;
                    items.forEach(it => {
                        // label includes qty and product name
                        const opt = document.createElement('option');
                        opt.value = it.id;
                        opt.text = `${it.product_name} — Qty: ${it.qty} — ₹${(Number(it.price)||0).toFixed(2)}`;
                        selectOrderItem.appendChild(opt);
                    });
                }

                selectOrder.addEventListener('change', function() {
                    const sel = this.selectedOptions[0];
                    if (!sel || !sel.value) {
                        resetCustomerPreview();
                        selectOrderItem.innerHTML = '<option value="">Select order → pick item</option>';
                        return;
                    }

                    // populate preview & hidden user id
                    const userId = sel.dataset.userId || '';
                    const name = sel.dataset.customerName || '—';
                    const email = sel.dataset.customerEmail || '';

                    custName.textContent = name;
                    custEmail.textContent = email ? email : '';
                    hiddenUserId.value = userId;

                    // parse items JSON (data-items)
                    try {
                        const items = JSON.parse(sel.dataset.items || '[]');
                        populateItems(items);
                    } catch (e) {
                        populateItems([]);
                        console.error('Failed to parse order items JSON', e);
                    }
                });

                // If page loads with old input or preselected option, trigger change to populate fields
                if (selectOrder.value) {
                    selectOrder.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush

@endsection
