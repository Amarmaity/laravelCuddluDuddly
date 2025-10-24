@extends('admin.layouts.admin')

@section('title', 'Return Details')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-3">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <span
                class="badge fs-6 py-2 px-3 shadow-sm rounded-pill bg-{{ match ($return->status) {
                    'requested' => 'warning text-dark',
                    'approved' => 'info text-dark',
                    'received' => 'secondary',
                    'refunded' => 'success',
                    'rejected' => 'danger',
                    default => 'light text-dark',
                } }}">
                <i class="bi bi-circle-fill small me-1"></i> {{ ucfirst($return->status) }}
            </span>
        </div>

        <div class="row g-4">

            <!-- Main Left Section -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #2563eb, #0ea5e9);">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-return-left me-2 fs-5"></i>
                            <h5 class="mb-0 fw-semibold">Return #{{ $return->return_number }}</h5>
                        </div>
                    </div>

                    <div class="card-body bg-light">
                        <!-- Customer & Return Info -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="bg-white shadow-sm rounded-3 p-3 border-start border-4 border-primary">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-2">
                                        <i class="bi bi-receipt me-1 text-primary"></i> Return Information
                                    </h6>
                                    <ul class="list-unstyled mb-0 small lh-lg">
                                        <li><strong>Order:</strong> {{ $return->order->order_number ?? '—' }}</li>
                                        <li><strong>Refund Method:</strong> {{ ucfirst($return->refund_method ?? '—') }}
                                        </li>
                                        <li><strong>Refund Amount:</strong>
                                            {{ $return->refund_amount ? '₹' . number_format($return->refund_amount, 2) : '—' }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white shadow-sm rounded-3 p-3 border-start border-4 border-success">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-2">
                                        <i class="bi bi-person-circle me-1 text-success"></i> Customer
                                    </h6>
                                    <ul class="list-unstyled mb-0 small lh-lg">
                                        <li><strong>Name:</strong> {{ $return->user->full_name ?? '—' }}</li>
                                        <li><strong>Email:</strong> {{ $return->user->email ?? '—' }}</li>
                                        <li><strong>Contact:</strong> {{ $return->user->phone ?? '—' }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Product + Reason Section -->
                        <div class="bg-white shadow-sm rounded-3 mt-4 p-3 border-start border-4 border-warning">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="flex-fill pe-3">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-2">
                                        <i class="bi bi-box-seam me-1 text-warning"></i> Product Details
                                    </h6>
                                    <p class="small mb-1"><strong>Product:</strong>
                                        {{ $return->orderItem->product->name ?? '—' }}</p>
                                    <p class="small mb-0"><strong>Quantity:</strong>
                                        {{ $return->orderItem->quantity ?? '—' }}</p>
                                </div>
                                <div class="flex-fill ps-3 border-start border-2 border-light">
                                    <h6 class="text-uppercase text-muted small fw-semibold mb-2">
                                        <i class="bi bi-chat-left-text me-1 text-warning"></i> Reason
                                    </h6>
                                    <p class="small text-dark mb-0">{{ $return->reason ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Animated Status Timeline -->
                        <div class="mt-4">
                            @php
                                $stages = ['requested', 'approved', 'received', 'refunded', 'rejected'];
                                $currentIndex = array_search($return->status, $stages);
                            @endphp
                            <div class="timeline-container mt-3">
                                @foreach ($stages as $index => $stage)
                                    <div
                                        class="timeline-step {{ $index <= $currentIndex && $return->status !== 'rejected' ? 'active' : ($stage === 'rejected' && $return->status === 'rejected' ? 'rejected' : '') }}">
                                        <div class="dot">
                                            <i
                                                class="bi {{ match ($stage) {
                                                    'requested' => 'bi-envelope-open',
                                                    'approved' => 'bi-check-circle',
                                                    'received' => 'bi-box-seam',
                                                    'refunded' => 'bi-cash-stack',
                                                    'rejected' => 'bi-x-circle',
                                                    default => 'bi-dot',
                                                } }}"></i>
                                        </div>
                                        <div class="label">{{ ucfirst($stage) }}</div>
                                    </div>
                                @endforeach
                                <div class="timeline-line"></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Update Form -->
                        <form action="{{ route('admin.returns.update', $return->id) }}" method="POST"
                            class="row g-3 align-items-end">
                            @csrf
                            @method('PUT')

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Status</label>
                                <select name="status" class="form-select rounded-3 shadow-sm" required>
                                    @foreach ($stages as $status)
                                        <option value="{{ $status }}"
                                            {{ $return->status === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Refund Amount (₹)</label>
                                <input type="number" name="refund_amount" class="form-control rounded-3 shadow-sm"
                                    value="{{ $return->refund_amount ?? '' }}" step="0.01" placeholder="Enter amount">
                            </div>

                            <div class="col-md-4 text-md-end">
                                <button type="submit"
                                    class="btn btn-gradient-success px-4 rounded-3 shadow-sm fw-semibold">
                                    <i class="bi bi-check2-circle me-1"></i> Update Return
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-white border-0 fw-semibold text-secondary">
                        <i class="bi bi-info-circle me-1"></i> Return Summary
                    </div>
                    <div class="card-body small text-muted">
                        <p><strong>Order Number:</strong><br>{{ $return->order->order_number ?? '—' }}</p>
                        <p><strong>Order Date:</strong><br>{{ $return->order->created_at->format('d M Y') ?? '—' }}</p>
                        <p><strong>Payment Method:</strong><br>{{ ucfirst($return->order->payment_method ?? '—') }}</p>
                        <p><strong>Total Value:</strong><br>₹{{ number_format($return->order->total ?? 0, 2) }}</p>

                        <hr>

                        <h6 class="text-uppercase small text-muted fw-semibold mb-2">Product & Reason</h6>
                        <p class="mb-1"><strong>Product:</strong> {{ $return->orderItem->product->name ?? '—' }}</p>
                        <p class="mb-3"><strong>Reason:</strong> {{ $return->reason ?? '—' }}</p>

                        <hr>

                        <p><strong>Requested:</strong><br>{{ $return->created_at->format('d M Y, h:i A') }}</p>
                        <p><strong>Last Updated:</strong><br>{{ $return->updated_at->format('d M Y, h:i A') }}</p>

                        <div class="mt-3 d-grid gap-2">
                            <a href="{{ route('admin.orders.show', $return->order->id) }}"
                                class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-eye me-1"></i> View Order
                            </a>
                            <a href="{{ route('admin.customers.show', $return->user->id) }}"
                                class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-person me-1"></i> View Customer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .btn-gradient-success {
                background: linear-gradient(135deg, #16a34a, #22c55e);
                color: #fff;
                border: none;
            }

            .btn-gradient-success:hover {
                background: linear-gradient(135deg, #15803d, #16a34a);
                color: #fff;
            }

            /* ✨ Animated Timeline */
            .timeline-container {
                position: relative;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 1rem;
            }

            .timeline-line {
                position: absolute;
                top: 50%;
                left: 0;
                width: 100%;
                height: 4px;
                background: #e5e7eb;
                z-index: 0;
                border-radius: 2px;
                overflow: hidden;
            }

            .timeline-line::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                width: {{ ($currentIndex + 1) * (100 / count($stages)) }}%;
                background: linear-gradient(90deg, #22c55e, #16a34a);
                animation: progress 1.2s ease-out;
                border-radius: 2px;
            }

            @keyframes progress {
                from {
                    width: 0;
                    opacity: 0.3;
                }

                to {
                    opacity: 1;
                }
            }

            .timeline-step {
                text-align: center;
                position: relative;
                z-index: 1;
                flex: 1;
            }

            .timeline-step .dot {
                width: 36px;
                height: 36px;
                margin: 0 auto;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f3f4f6;
                border: 2px solid #d1d5db;
                color: #9ca3af;
                transition: all 0.3s ease;
            }

            .timeline-step.active .dot {
                background: #22c55e;
                color: #fff;
                border-color: #16a34a;
                transform: scale(1.05);
                box-shadow: 0 0 10px rgba(34, 197, 94, 0.4);
            }

            .timeline-step.rejected .dot {
                background: #dc2626;
                color: #fff;
                border-color: #b91c1c;
                box-shadow: 0 0 8px rgba(220, 38, 38, 0.4);
            }

            .timeline-step .label {
                margin-top: 6px;
                font-size: 0.8rem;
                font-weight: 600;
                color: #6b7280;
            }
        </style>
    @endpush
@endsection
