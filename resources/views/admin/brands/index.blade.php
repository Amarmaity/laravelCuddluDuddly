@extends('admin.layouts.admin')

@section('title', 'Manage Brands')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-2">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-semibold text-primary">
                <i class="bi bi-tags me-2"></i> Brands
            </h4>
            <a href="{{ route('admin.brands.create') }}" class="btn btn-gradient-primary btn-sm shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> New Brand
            </a>
        </div>

        <!-- Search Filter -->
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body bg-light rounded-4">
                <form method="GET" class="row gy-2 gx-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm shadow-sm" placeholder="Search by Brand Name">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted small">Status</label>
                        <select name="status" class="form-select form-select-sm shadow-sm">
                            <option value="" selected>All</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-sm btn-primary shadow-sm rounded-pill px-3">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.brands.index') }}"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Brands Table -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-nowrap">
                        <tr>
                            <th>#</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Added At</th>
                            <th>Updated At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                            <tr class="border-bottom">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="Logo" class="rounded"
                                            style="width:50px;height:50px;object-fit:cover;">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $brand->name }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $brand->created_at->format('d M, Y') }}</td>
                                <td>{{ $brand->updated_at->format('d M, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm rounded-pill shadow-sm delete-btn"
                                            type="submit" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No brands found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($brands && $brands->hasPages())
                <div class="card-footer">
                    {{ $brands->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

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
    <script src="{{ asset('js/brands.js') }}"></script>
@endpush
