@extends('admin.layouts.admin')

@section('title', 'Seller Support Tickets')

@section('content')
    <div class="container py-4">
        <h4 class="mb-3"><i class="bi bi-headset me-2"></i> Seller Support Tickets</h4>
        <div class="card shadow-sm border-0 mb-3 p-2">
            {{-- 🔍 Search & Filter --}}
            <form method="GET" action="{{ route('admin.seller-supports.index') }}" class="row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="By Subject, Seller Name, or Product">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['open' => 'Open', 'pending' => 'Pending', 'close' => 'Close'] as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-dark"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </form>

        </div>



        {{-- 🧾 Support Tickets Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Sl No</th>
                        <th>Seller</th>
                        <th>Subject</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Submitted On</th>
                        <th>Admin</th>
                        <th class="text-end">Options</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supports as $index => $support)
                        <tr>
                            <td>{{ $supports->firstItem() + $index }}</td>
                            <td>
                                {{ $support->seller->name ?? '—' }}<br>
                                <small>{{ $support->seller->email ?? '' }}</small>
                            </td>
                            <td>{{ $support->subject }}</td>
                            <td>
                                @if($support->product)
                                    <div class="d-flex align-items-center">
                                        @php
                                            $imagePath = $support->product->primaryImage->image_path ?? null;
                                        @endphp
                                        @if($imagePath)
                                            <img src="{{ asset('storage/images/' . $imagePath) }}" alt="{{ $support->product->name }}"
                                                width="60" height="60" class="rounded me-2">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" width="60" height="60" class="rounded me-2">
                                        @endif
                                        <span>{{ $support->product->name }}</span>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @php $colors = ['open' => 'success', 'pending' => 'warning', 'close' => 'secondary']; @endphp
                                <span class="badge bg-{{ $colors[$support->status] ?? 'secondary' }}">
                                    {{ ucfirst($support->status) }}
                                </span>
                            </td>
                            <td>{{ $support->created_at->format('d M, Y') }}</td>
                            <td>{{ $support->admin->name ?? '—' }}</td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.seller-supports.show', $support->id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <button class="dropdown-item delete-btn" data-id="{{ $support->id }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No support tickets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 📄 Pagination --}}
        <div class="mt-3">{{ $supports->links() }}</div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    if (!confirm("Are you sure you want to delete this ticket?")) return;

                    fetch(`/admin/seller-supports/${btn.dataset.id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        }
                    }).then(res => res.json())
                        .then(data => {
                            if (data.success) btn.closest('tr').remove();
                            else alert("Failed to delete.");
                        }).catch(() => alert("Failed to delete."));
                });
            });
        });
    </script>
@endpush