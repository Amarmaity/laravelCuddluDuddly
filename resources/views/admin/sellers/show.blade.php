@extends('admin.layouts.admin')

@section('title', 'Seller Application Details')

@section('content')
    <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-0">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2 text-primary"></i> Seller Application Details
            </h5>
            <a href="{{ route('admin.sellers.index') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>

        <!-- Body -->
        <div class="card-body">
            <div class="row g-4">
                <!-- Left column -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Basic Information</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-shop me-2 text-secondary"></i>
                            <strong>Business Name:</strong> {{ $seller->name }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-person me-2 text-secondary"></i>
                            <strong>Contact Person:</strong> {{ $seller->contact_person }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2 text-secondary"></i>
                            <strong>Email:</strong> {{ $seller->email }}
                        </li>
                        <li>
                            <i class="bi bi-telephone me-2 text-secondary"></i>
                            <strong>Phone:</strong> {{ $seller->phone }}
                        </li>
                    </ul>
                </div>

                <!-- Right column -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Application Status</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-flag me-2 text-secondary"></i>
                            <strong>Status:</strong>
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">
                                {{ ucfirst($seller->application_status) }}
                            </span>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-shield-check me-2 text-secondary"></i>
                            <strong>Compliance:</strong>
                            <span
                                class="badge bg-{{ $seller->compliance_status === 'approved' ? 'success' : ($seller->compliance_status === 'rejected' ? 'danger' : 'secondary') }} px-3 py-1 rounded-pill">
                                {{ ucfirst($seller->compliance_status ?? 'Pending') }}
                            </span>
                        </li>
                        <li>
                            <i class="bi bi-toggle-on me-2 text-secondary"></i>
                            <strong>Active:</strong>
                            <span
                                class="badge bg-{{ $seller->is_active ? 'success' : 'secondary' }} px-3 py-1 rounded-pill">
                                {{ $seller->is_active ? 'Yes' : 'No' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-4">

            <!-- Documents -->
            <div class="mb-4">
                <h6 class="text-muted mb-3">Documents</h6>
                @if ($seller->documents)
                    <a href="{{ route('admin.seller-applications.viewDocs', $seller) }}"
                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm">
                        <i class="bi bi-download me-1"></i> Download Documents
                    </a>
                @else
                    <span class="text-muted">
                        <i class="bi bi-file-earmark-x me-1"></i> No documents uploaded
                    </span>
                @endif
            </div>

            <!-- Actions -->
            @if ($seller->compliance_status === 'pending')
                <div class="d-flex gap-3">
                    <!-- Approve -->
                    <form action="{{ route('admin.sellers.compliance.accept', $seller) }}" method="POST"
                        onsubmit="return confirm('Approve this seller?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success rounded-pill shadow-sm px-4">
                            <i class="bi bi-check-circle me-1"></i> Approve
                        </button>
                    </form>

                    <!-- Reject -->
                    <form action="{{ route('admin.sellers.compliance.reject', $seller) }}" method="POST"
                        onsubmit="return confirm('Reject this seller?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger rounded-pill shadow-sm px-4">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
