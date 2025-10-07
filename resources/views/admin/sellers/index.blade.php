@extends('admin.layouts.admin')

@section('title', 'Sellers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-shop me-2"></i> All Sellers</h4>
        <a href="{{ route('admin.sellers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Seller
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sellers.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="By Name, email, phone, contact, pin code or city">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest Registered
                        </option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Registered
                        </option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="products" {{ request('sort') == 'products' ? 'selected' : '' }}>Products Count
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><i class="bi bi-person-badge"></i> Name</th>
                        <th><i class="bi bi-person"></i> Contact</th>
                        <th><i class="bi bi-envelope"></i> Email</th>
                        <th><i class="bi bi-telephone"></i> Phone</th>
                        <th><i class="bi bi-toggle-on"></i> Status</th>
                        <th class="text-center"><i class="bi bi-gear"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sellers as $seller)
                        <tr data-id="{{ $seller->id }}">
                            <td>{{ $seller->id }}</td>
                            <td>{{ $seller->name }}</td>
                            <td>{{ $seller->contact_person }}</td>
                            <td>{{ $seller->email }}</td>
                            <td>{{ $seller->phone }}</td>
                            <td>
                                <span class="badge bg-{{ $seller->is_active ? 'success' : 'secondary' }}">
                                    {{ $seller->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.sellers.show', $seller) }}" class="btn btn-sm btn-info me-1">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.sellers.edit', $seller) }}" class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $seller->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="bi bi-info-circle"></i> No sellers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $sellers->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("select-all")?.addEventListener("change", function() {
                document.querySelectorAll(".row-checkbox").forEach(cb => cb.checked = this.checked);
            });

            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    if (confirm("Are you sure you want to delete this seller?")) {
                        fetch(`/admin/sellers/${btn.dataset.id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            }
                        }).then(() => location.reload());
                    }
                });
            });
        });
    </script>
@endpush
