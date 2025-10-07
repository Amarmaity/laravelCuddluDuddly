@extends('admin.layouts.admin')

@section('title', 'Seller Compliance (KYC)')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3"><i class="bi bi-shield-check me-2"></i>KYC / Compliance</h5>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="kycTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                        type="button" role="tab">
                        Pending
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected"
                        type="button" role="tab">
                        Rejected
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="kycTabsContent">
                <!-- Pending Applications -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Business</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th class="text-center"><i class="bi bi-gear"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingKyc as $seller)
                                    <tr>
                                        <td>{{ $seller->id }}</td>
                                        <td>{{ $seller->name }}</td>
                                        <td>{{ $seller->email }}</td>
                                        <td>
                                            <span class="badge bg-warning">
                                                Pending
                                            </span>
                                        </td>
                                        <td>
                                            @if (!empty($seller->documents))
                                                <a href="{{ route('admin.seller-applications.viewDocs', $seller->id) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-eye me-1"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bi bi-file-earmark-x"></i> No Docs
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Verify -->
                                                <!-- Verify -->
                                                <form action="{{ route('admin.sellers.compliance.accept', $seller->id) }}"
                                                    method="POST" class="d-inline verify-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="btn btn-success btn-sm rounded-pill px-3 shadow-sm verify-btn">
                                                        <i class="bi bi-check2-circle me-1"></i> Verify
                                                    </button>
                                                </form>

                                                <!-- Reject (opens modal) -->
                                                <button type="button"
                                                    class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                    data-id="{{ $seller->id }}">
                                                    <i class="bi bi-x-circle me-1"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="bi bi-info-circle"></i> No pending KYC.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $pendingKyc->links() }}
                </div>

                <!-- Rejected Applications -->
                <div class="tab-pane fade" id="rejected" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Business</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rejectedKyc as $seller)
                                    <tr>
                                        <td>{{ $seller->id }}</td>
                                        <td>{{ $seller->name }}</td>
                                        <td>{{ $seller->email }}</td>
                                        <td><span class="badge bg-danger">Rejected</span></td>
                                        <td>{{ $seller->rejection_reason ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="bi bi-info-circle"></i> No rejected applications.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $rejectedKyc->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="4" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Reject Modal logic
                var rejectModal = document.getElementById('rejectModal');
                rejectModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var sellerId = button.getAttribute('data-id');
                    var form = rejectModal.querySelector('#rejectForm');
                    form.action = "{{ url('admin/seller-compliance') }}/" + sellerId + "/reject";
                });

                // Verify button loading state
                document.querySelectorAll('.verify-form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        var btn = form.querySelector('.verify-btn');
                        btn.disabled = true; // disable to prevent multiple clicks
                        btn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Verifying...
            `;
                    });
                });
            });
        </script>
    @endpush
@endsection
