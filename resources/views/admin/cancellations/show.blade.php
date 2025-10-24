@extends('admin.layouts.admin')

@section('title', 'Cancellation Details')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-3">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 fw-semibold text-primary">
                <i class="bi bi-x-octagon me-2"></i> Cancellation Request #{{ $cancellation->id }}
            </h4>
            <a href="{{ route('admin.cancellations.index') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4">

                <div class="row g-4">
                    <!-- Order Info -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <h6 class="fw-semibold border-bottom pb-2 mb-3 text-primary">
                                <i class="bi bi-box-seam me-2"></i> Order Information
                            </h6>
                            <p class="mb-1"><strong>Order No:</strong>
                                <a href="{{ route('admin.orders.show', $cancellation->order_id) }}"
                                    class="text-decoration-none text-dark">
                                    {{ $cancellation->order->order_number ?? '—' }}
                                </a>
                            </p>
                            <p class="mb-1"><strong>Order Date:</strong>
                                {{ optional($cancellation->order->created_at)->format('d M Y, h:i A') ?? '—' }}
                            </p>
                            <p class="mb-0"><strong>Order Status:</strong>
                                <span class="badge bg-info-subtle text-info">
                                    {{ ucfirst($cancellation->order->order_status ?? 'N/A') }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <h6 class="fw-semibold border-bottom pb-2 mb-3 text-primary">
                                <i class="bi bi-person-circle me-2"></i> Customer Information
                            </h6>
                            @if ($cancellation->user)
                                <p class="mb-1"><strong>Name:</strong> {{ $cancellation->user->full_name }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $cancellation->user->email }}</p>
                                <p class="mb-0"><strong>Phone:</strong> {{ $cancellation->user->phone ?? '—' }}</p>
                            @else
                                <p class="text-muted">User account no longer exists.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cancellation Info -->
                <div class="mt-4">
                    <div class="p-3 bg-white border rounded-4">
                        <h6 class="fw-semibold border-bottom pb-2 mb-3 text-primary">
                            <i class="bi bi-info-circle me-2"></i> Cancellation Details
                        </h6>
                        <p class="mb-2"><strong>Reason:</strong></p>
                        <p class="text-muted small mb-3">{{ $cancellation->reason }}</p>

                        @php
                            $badgeColors = [
                                'pending' => 'warning text-dark',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            ];
                            $badgeClass = $badgeColors[$cancellation->status] ?? 'secondary';
                        @endphp

                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge bg-{{ $badgeClass }} rounded-pill px-3">
                                {{ ucfirst($cancellation->status) }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Requested On:</strong>
                            {{ $cancellation->created_at->format('d M Y, h:i A') }}
                        </p>

                        @if ($cancellation->approved_at)
                            <p class="mb-1"><strong>Approved On:</strong>
                                {{ $cancellation->approved_at->format('d M Y, h:i A') }}
                            </p>
                        @endif

                        @if ($cancellation->approvedBy)
                            <p class="mb-0"><strong>Approved By:</strong>
                                {{ $cancellation->approvedBy->name }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    @if ($cancellation->status === 'pending')
                        <form action="{{ route('admin.cancellations.approve', $cancellation->id) }}" method="POST"
                            onsubmit="return confirm('Approve this cancellation request?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-gradient-primary btn-sm shadow-sm rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Approve
                            </button>
                        </form>

                        <form action="{{ route('admin.cancellations.reject', $cancellation->id) }}" method="POST"
                            onsubmit="return confirm('Reject this cancellation request?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill shadow-sm">
                                <i class="bi bi-x-circle me-1"></i> Reject
                            </button>
                        </form>
                    @else
                        <button class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm" disabled>
                            <i class="bi bi-lock me-1"></i> Request {{ ucfirst($cancellation->status) }}
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .btn-gradient-primary {
                background: linear-gradient(135deg, #2563eb, #0ea5e9);
                color: #fff;
                border: none;
            }

            .btn-gradient-primary:hover {
                background: linear-gradient(135deg, #1d4ed8, #0284c7);
                color: #fff;
            }
        </style>
    @endpush
@endsection
