@extends('admin.layouts.admin')

@section('title', 'Cancellation Requests')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-0">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-semibold text-primary">
                <i class="bi bi-x-octagon me-2"></i> Cancellation Requests
            </h4>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body bg-light rounded-4">
                <form method="GET" class="row gy-2 gx-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm shadow-sm" placeholder="Search by Order No. or User">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Status</label>
                        <select name="status" class="form-select form-select-sm shadow-sm">
                            <option value="">All Status</option>
                            @foreach (['pending', 'approved', 'rejected'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="form-control form-control-sm shadow-sm">
                    </div>

                    <div class="col-md-3 text-end">
                        <button class="btn btn-sm btn-primary shadow-sm rounded-pill px-3">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.cancellations.index') }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cancellation Table -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-danger text-nowrap">
                        <tr>
                            <th>#</th>
                            <th>Order No.</th>
                            <th>Customer</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Requested On</th>
                            <th>Approved By</th>
                            <th>Approved At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cancellations as $index => $cancellation)
                            <tr class="border-bottom">
                                <td>{{ $index + $cancellations->firstItem() }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $cancellation->order_id) }}"
                                        class="text-decoration-none fw-semibold text-dark">
                                        {{ $cancellation->order->order_number ?? '—' }}
                                    </a>
                                </td>
                                <td>
                                    {{ $cancellation->user->full_name ?? '—' }}<br>
                                    <small class="text-muted">{{ $cancellation->user->email ?? '' }}</small>
                                </td>
                                <td class="text-muted small">{{ Str::limit($cancellation->reason, 40) }}</td>
                                <td>
                                    @php
                                        $colors = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                        ];
                                    @endphp
                                    <span
                                        class="badge rounded-pill bg-{{ $colors[$cancellation->status] ?? 'secondary' }}">
                                        {{ ucfirst($cancellation->status) }}
                                    </span>
                                </td>
                                <td>{{ $cancellation->created_at->format('d M Y') }}</td>
                                <td>{{ $cancellation->approvedBy->name ?? '—' }}</td>
                                <td>{{ $cancellation->approved_at ? $cancellation->approved_at->format('d M Y') : '—' }}
                                </td>
                                <td class="text-end">
                                    @if ($cancellation->status === 'pending')
                                        <form action="{{ route('admin.cancellations.approve', $cancellation->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-outline-success btn-sm rounded-pill shadow-sm me-1"
                                                onclick="return confirm('Approve this cancellation?')" title="Approve">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.cancellations.reject', $cancellation->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-outline-danger btn-sm rounded-pill shadow-sm me-1"
                                                onclick="return confirm('Reject this cancellation?')" title="Reject">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.cancellations.show', $cancellation->id) }}"
                                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    No cancellation requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-0 bg-white py-3">
                {{ $cancellations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .btn-gradient-primary {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                color: #fff;
                border: none;
            }

            .btn-gradient-primary:hover {
                background: linear-gradient(135deg, #b91c1c, #dc2626);
                color: #fff;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el)
            })
        </script>
    @endpush
@endsection
