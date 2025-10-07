@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-cash-stack me-2"></i> Seller Payouts</h4>
            <a href="{{ route('admin.payouts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Initiate Payout
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-list-check me-2"></i> All Payouts</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Seller</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Method</th>
                                <th>Requested</th>
                                <th>Processed</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                                <tr>
                                    <td>{{ $payout->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $payout->seller->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $payout->seller->email ?? '' }}</small>
                                    </td>
                                    <td class="fw-semibold text-success">
                                        <i class="bi bi-currency-dollar"></i> {{ number_format($payout->amount, 2) }}
                                    </td>
                                    <td>
                                        @if ($payout->status == 'completed')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>
                                                Completed</span>
                                        @elseif($payout->status == 'failed')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Failed</span>
                                        @elseif($payout->status == 'processing')
                                            <span class="badge bg-warning text-dark"><i
                                                    class="bi bi-hourglass-split me-1"></i> Processing</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-clock me-1"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($payout->payment_method) }}</td>
                                    <td>{{ $payout->requested_at?->format('d M Y, h:i A') }}</td>
                                    <td>{{ $payout->processed_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.payouts.edit', $payout->id) }}"
                                            class="btn btn-sm btn-outline-warning me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.payouts.destroy', $payout->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this payout?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-2"></i> No payouts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>
@endsection