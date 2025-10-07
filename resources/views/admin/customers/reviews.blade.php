@extends('admin.layouts.admin')

@section('title', 'Reviews')

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        {{-- <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-star me-2"></i> Reviews</h2>
            <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Review
            </a>
        </div> --}}

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters & Search -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Customer, email, product, or review">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select">
                            <option value="">All</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }} Stars
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort By</label>
                        <select name="sort" class="form-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
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

        <!-- Reviews Table -->
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
                        Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:1%"><input type="checkbox" id="selectAll"></th>
                                <th style="width:1%">#</th>
                                <th><i class="bi bi-person me-1"></i> Customer Name</th>
                                <th><i class="bi bi-person-circle me-1"></i> Profile</th>
                                <th><i class="bi bi-envelope me-1"></i> Email</th>
                                <th><i class="bi bi-telephone me-1"></i> Mobile</th>
                                <th><i class="bi bi-box-seam me-1"></i> Product</th>
                                <th><i class="bi bi-image me-1"></i> Product Image</th>
                                <th><i class="bi bi-star-fill me-1 text-warning"></i> Rating</th>
                                <th><i class="bi bi-chat-left-text me-1"></i> Review</th>
                                <th><i class="bi bi-calendar me-1"></i> Date</th>
                                {{-- <th class="text-end">Actions</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reviews as $review)
                                <tr data-id="{{ $review->id }}">
                                    <td><input type="checkbox" name="selected[]" value="{{ $review->id }}"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $review->customer?->first_name ?? '-' }} {{ $review->customer?->last_name ?? '' }}
                                    </td>
                                    <td>
                                        <img src="{{ $review->customer?->avatar ?? asset('images/default-avatar.png') }}"
                                            class="rounded-circle" width="40" height="40" alt="profile">
                                    </td>
                                    <td>{{ $review->customer?->email ?? '-' }}</td>
                                    <td>{{ $review->customer?->phone ?? '-' }}</td>
                                    <td>{{ $review->product?->name ?? '-' }}</td>
                                    <td>
                                        <img src="{{ $review->product?->image ?? asset('images/no-image.png') }}"
                                            class="rounded" width="50" height="50" alt="product">
                                    </td>
                                    <td><span class="badge bg-warning text-dark">{{ $review->rating }}/5</span></td>
                                    <td>{{ Str::limit($review->comment, 50) ?? '-' }}</td>
                                    <td>{{ $review->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4">
                                        <i class="bi bi-exclamation-circle me-2"></i> No reviews found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end align-items-center p-3 border-top">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';
            const selectAll = document.getElementById('selectAll');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

            function allCheckboxes() {
                return document.querySelectorAll('input[name="selected[]"]');
            }

            function updateDeleteButton() {
                const anyChecked = [...allCheckboxes()].some(cb => cb.checked);
                deleteSelectedBtn.disabled = !anyChecked;
            }

            selectAll?.addEventListener('change', function (e) {
                allCheckboxes().forEach(cb => cb.checked = e.target.checked);
                updateDeleteButton();
            });

            document.addEventListener('change', function (e) {
                if (e.target.matches('input[name="selected[]"]')) updateDeleteButton();
            });

            deleteSelectedBtn?.addEventListener('click', function () {
                const ids = [...document.querySelectorAll('input[name="selected[]"]:checked')].map(cb => cb.value);
                if (!ids.length) return;
                if (!confirm(`Delete ${ids.length} reviews? This action cannot be undone.`)) return;

                fetch("{{ route('admin.customers.bulkDelete') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            ids.forEach(id => document.querySelector(`tr[data-id="${id}"]`)?.remove());
                            deleteSelectedBtn.disabled = true;
                        } else {
                            alert(data.message || 'Failed to delete reviews.');
                        }
                    })
                    .catch(() => alert('Something went wrong while deleting.'));
            });
        });
    </script>
@endpush