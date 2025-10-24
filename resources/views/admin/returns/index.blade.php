@extends('admin.layouts.admin')

@section('title', 'Return Requests')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-2">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-semibold text-primary">
                <i class="bi bi-arrow-return-left me-2"></i> Return Requests
            </h4>
            <a href="{{ route('admin.returns.create') }}" class="btn btn-gradient-primary btn-sm shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> New Return
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body bg-light rounded-4">
                <form method="GET" class="row gy-2 gx-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm shadow-sm" placeholder="Search by Order / Return No.">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Status</label>
                        <select name="status" class="form-select form-select-sm shadow-sm">
                            <option value="">All Status</option>
                            @foreach (['requested', 'approved', 'received', 'refunded', 'rejected'] as $status)
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
                        <a href="{{ route('admin.returns.index') }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Return Table -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-nowrap">
                        <tr>
                            <th>#</th>
                            <th>Return No.</th>
                            <th>Order No.</th>
                            <th>Customer</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Refund</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                            <tr class="border-bottom">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary">{{ $return->return_number }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $return->order_id) }}"
                                        class="text-decoration-none fw-semibold text-dark">
                                        {{ $return->order->order_number ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $return->user->full_name ?? '—' }}</td>
                                <td class="text-muted small">{{ Str::limit($return->reason, 35) }}</td>
                                <td>
                                    @php
                                        $colors = [
                                            'requested' => 'secondary',
                                            'approved' => 'info',
                                            'rejected' => 'danger',
                                            'received' => 'warning',
                                            'refunded' => 'success',
                                        ];
                                    @endphp
                                    <span class="badge rounded-pill bg-{{ $colors[$return->status] ?? 'secondary' }}">
                                        {{ ucfirst($return->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $return->refund_amount ? '₹' . number_format($return->refund_amount, 2) : '—' }}
                                </td>
                                <td>{{ $return->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.returns.show', $return->id) }}"
                                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm me-1"
                                        title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.returns.destroy', $return->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm rounded-pill shadow-sm"
                                            onclick="return confirm('Delete this return?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No return requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-0 bg-white py-3">
                {{ $returns->appends(request()->query())->links() }}
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

    @push('scripts')
        <script>
            // Bootstrap tooltips init
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el)
            })
        </script>
    @endpush
@endsection
