@extends('admin.layouts.admin')

@section('title', 'Wishlist Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-list-check me-2"></i> User Wishlists</h4>
        <button id="bulk-delete-btn" class="btn btn-danger">
            <i class="bi bi-trash me-1"></i> Delete Selected
        </button>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('wishlists.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="By Product, Price or User Name">
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
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Sl No</th>
                        <th>Product</th>
                        <th>User</th>
                        <th>Price</th>
                        <th>Added On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wishlist as $index => $item)
                        <tr id="wishlist-item-{{ $item->id }}">
                            <td><input type="checkbox" class="wishlist-checkbox" value="{{ $item->id }}"></td>
                            <td>{{ $index + 1 }}</td>
                            <td class="d-flex align-items-center">
                                @php
                                    $productImage = $item->product->primaryImage->image_path ?? null;
                                    $imagePath = $productImage ? str_replace('products/', '', $productImage) : null;
                                @endphp

                                @if($imagePath)
                                    <img src="{{ asset('storage/images/products/' . $imagePath) }}"
                                        alt="{{ $item->product->name ?? 'No Image' }}" width="60" height="60" class="me-2 rounded">
                                @else
                                    <div style="color:red; font-size: 13px;">No image available.</div>
                                @endif

                                <div class="text-truncate" style="max-width: 250px;">
                                    <strong>{{ $item->product->name ?? 'Unknown Product' }}</strong><br>
                                    <small>{{ Str::limit($item->product->description ?? '', 200) }}</small>
                                </div>
                            </td>

                            <td>
                                {{ $item->user->first_name ?? 'Unknown' }} {{ $item->user->last_name ?? '' }}<br>
                                <small>{{ $item->user->email ?? '' }}</small>
                            </td>

                            <td>₹{{ number_format($item->product->price ?? 0, 2) }}</td>
                            <td>{{ $item->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="bi bi-info-circle"></i> No wishlist items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Select/Deselect all checkboxes
            document.getElementById("select-all")?.addEventListener("change", function () {
                document.querySelectorAll(".wishlist-checkbox").forEach(cb => cb.checked = this.checked);
            });

            // Single Delete
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.dataset.id;
                    if (confirm("Are you sure you want to delete this item?")) {
                        fetch('{{ route("wishlists.destroy", ":id") }}'.replace(':id', id), {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById(`wishlist-item-${id}`).remove();
                                } else {
                                    alert('Failed to delete item.');
                                }
                            })
                            .catch(() => alert('Failed to delete item.'));
                    }
                });
            });

            // Bulk Delete
            document.getElementById("bulk-delete-btn")?.addEventListener("click", () => {
                let ids = Array.from(document.querySelectorAll(".wishlist-checkbox:checked")).map(cb => cb.value);
                if (ids.length === 0) return alert("Select at least one item to delete.");
                if (!confirm("Are you sure you want to delete selected items?")) return;

                fetch('{{ route("wishlist.bulk-delete") }}', {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ ids: ids })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            ids.forEach(id => document.getElementById(`wishlist-item-${id}`).remove());
                        } else {
                            alert("Failed to delete items.");
                        }
                    })
                    .catch(() => alert("Failed to delete items."));
            });
        });
    </script>
@endpush