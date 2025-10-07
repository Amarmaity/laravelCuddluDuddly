@extends('admin.layouts.admin')

@section('title', 'Customers')

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-people me-2"></i> Customers</h2>
            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Customer
            </a>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters & Search -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Name, email or phone">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
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
                            <option value="orders" {{ request('sort') == 'orders' ? 'selected' : '' }}>Orders Count</option>
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

        <!-- Customers Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <!-- Bulk actions -->
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" id="deleteSelectedBtn" class="btn btn-danger btn-sm" disabled>
                            <i class="bi bi-trash me-1"></i> Delete Selected
                        </button>
                    </div>
                    <div class="text-muted small">
                        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of
                        {{ $customers->total() }} customers
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:1%"><input type="checkbox" id="selectAll"></th>
                                <th style="width:1%">#</th>
                                <th><i class="bi bi-person me-1"></i> Name</th>
                                <th><i class="bi bi-envelope me-1"></i> Email</th>
                                <th><i class="bi bi-telephone me-1"></i> Phone</th>
                                <th><i class="bi bi-calendar me-1"></i> Registered</th>
                                <th><i class="bi bi-cart me-1"></i> Orders</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $customer)
                                <tr data-id="{{ $customer->id }}">
                                    <td><input type="checkbox" name="selected[]" value="{{ $customer->id }}"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td>{{ $customer->created_at->format('d M Y') }}</td>
                                    <td>{{ $customer->orders_count ?? 0 }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center">
                                            <!-- Status Toggle -->
                                            <div class="form-check form-switch me-2">
                                                <input type="checkbox" class="form-check-input status-toggle"
                                                    data-id="{{ $customer->id }}"
                                                    {{ $customer->status == 'active' ? 'checked' : '' }}>
                                            </div>

                                            <!-- View -->
                                            <a href="{{ route('admin.customers.show', $customer) }}"
                                                class="btn btn-sm btn-outline-info me-1">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <!-- Edit -->
                                            <a href="{{ route('admin.customers.edit', $customer) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-exclamation-circle me-2"></i> No customers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (kept separate at bottom) -->
                <div class="d-flex justify-content-end align-items-center p-3 border-top">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                '{{ csrf_token() }}';

            const selectAll = document.getElementById('selectAll');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

            // Helper: returns live NodeList of checkboxes
            function allCheckboxes() {
                return document.querySelectorAll('input[name="selected[]"]');
            }

            // Enable/disable delete selected button
            function updateDeleteButton() {
                const anyChecked = [...allCheckboxes()].some(cb => cb.checked);
                deleteSelectedBtn.disabled = !anyChecked;
            }

            // Select / Deselect all
            selectAll?.addEventListener('change', function(e) {
                allCheckboxes().forEach(cb => cb.checked = e.target.checked);
                updateDeleteButton();
            });

            // Update on individual checkbox change (event delegation)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.matches('input[name="selected[]"]')) {
                    updateDeleteButton();
                }
            });

            // Small helper to create bootstrap alert
            function createAlert(type, message) {
                const icon = (type === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
                const wrapper = document.createElement('div');
                wrapper.className = `alert alert-${type} alert-dismissible fade show shadow-sm mt-3`;
                wrapper.innerHTML = `
            <i class="bi ${icon} me-2"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                const container = document.querySelector('.container-fluid') || document.body;
                container.prepend(wrapper);
            }

            // STATUS TOGGLE (delegated)
            document.addEventListener('change', function(e) {
                if (!e.target.matches('.status-toggle')) return;

                const toggle = e.target;
                const id = toggle.dataset.id;
                const urlTemplate = "{{ route('admin.customers.toggle-status', ':id') }}";
                const url = urlTemplate.replace(':id', id);
                const checked = toggle.checked;

                fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            status: checked ? 'active' : 'inactive'
                        })
                    })
                    .then(res => res.json().catch(() => ({
                        success: false,
                        message: 'Invalid response'
                    })))
                    .then(data => {
                        if (data && data.success) {
                            createAlert('success', data.message || 'Status updated.');
                        } else {
                            createAlert('danger', data?.message || 'Failed to update status.');
                            // rollback toggle on failure
                            toggle.checked = !checked;
                        }
                    })
                    .catch(() => {
                        createAlert('danger', 'Something went wrong while updating status.');
                        toggle.checked = !checked;
                    });
            });

            // BULK DELETE: Open universal modal (preferred) or fallback AJAX
            deleteSelectedBtn?.addEventListener('click', function() {
                const ids = [...document.querySelectorAll('input[name="selected[]"]:checked')].map(cb => cb
                    .value);
                if (!ids.length) return;

                if (typeof window.openDeleteModal === 'function') {
                    // use universal modal (defined in partial)
                    openDeleteModal({
                        url: "{{ route('admin.customers.bulkDelete') }}",
                        ids: ids,
                        message: `Are you sure you want to delete ${ids.length} selected customer(s)?`,
                        ajax: true,
                        onSuccess: function(data) {
                            ids.forEach(id => document.querySelector(`tr[data-id="${id}"]`)
                                ?.remove());
                        }
                    });
                    return;
                }

                // fallback: if openDeleteModal not available, do AJAX directly
                if (!confirm(`Delete ${ids.length} customers? This action cannot be undone.`)) return;

                fetch("{{ route('admin.customers.bulkDelete') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ids
                        })
                    })
                    .then(res => res.json().catch(() => ({
                        success: false,
                        message: 'Invalid response'
                    })))
                    .then(data => {
                        if (data && data.success) {
                            // remove rows
                            ids.forEach(id => {
                                const row = document.querySelector(`tr[data-id="${id}"]`);
                                if (row) row.remove();
                            });
                            createAlert('success', data.message || 'Selected customers deleted.');
                            updateDeleteButton();
                        } else {
                            createAlert('danger', data?.message || 'Failed to delete customers.');
                        }
                    })
                    .catch(() => createAlert('danger', 'Something went wrong while deleting.'));
            });
        });
    </script>
@endpush
