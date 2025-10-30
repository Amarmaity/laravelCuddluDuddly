@extends('admin.layouts.admin')

@section('title', 'Seller Support Tickets')

@section('content')
    <div class="container py-4">
        <h4 class="mb-3"><i class="bi bi-headset me-2"></i> Seller Support Tickets</h4>

        {{-- 🔍 Search & Filter --}}
        <div class="card shadow-sm border-0 mb-3 p-2">
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
                        @foreach(['open' => 'Open', 'pending' => 'Pending', 'closed' => 'Closed', 'reopen' => 'Reopen'] as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
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
                        <th>Ticket ID</th>
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
                            <td>
                                {{ $support->id ?? '-' }}
                            </td>
                            <td>
                                {{ $support->seller->name ?? '—' }}<br>
                                <small>{{ $support->seller->email ?? '' }}</small>
                            </td>
                            <td>{{ $support->subject }}</td>
                            <td>
                                @if($support->product)
                                    <div class="d-flex align-items-center">
                                        @php $imagePath = $support->product->primaryImage->image_path ?? null; @endphp
                                        @if($imagePath)
                                            <img src="{{ asset('storage/images/' . $imagePath) }}" alt="{{ $support->product->name }}"
                                                width="50" height="50" class="rounded me-2">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" width="50" height="50" class="rounded me-2">
                                        @endif
                                        <span>{{ $support->product->name }}</span>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @php
                                    $colors = [
                                        'open' => 'success',
                                        'pending' => 'warning',
                                        'closed' => 'secondary',
                                        'reopened' => 'primary',
                                    ];
                                @endphp

                                <span class="badge bg-{{ $colors[$support->status] ?? 'secondary' }}">
                                    {{ ucfirst($support->status) }}
                                </span>

                            </td>
                            <td>{{ $support->created_at->format('d M, Y') }}</td>
                            <td>
                                @if($support->status === 'closed')
                                    <small class="text-muted">
                                        Closed by: {{ $support->closedBy->name ?? 'Unknown' }} <br>
                                        {{ $support->closed_at ? $support->closed_at->format('d M Y, H:i') : '' }}
                                    </small>
                                @elseif($support->status === 'reopened')
                                    <small class="text-muted">
                                        Reopened by: {{ $support->reopenedBy->name ?? 'Unknown' }} <br>
                                        {{ $support->reopened_at ? $support->reopened_at->format('d M Y, H:i') : '' }}
                                    </small>
                                @else
                                    <small class="text-muted">
                                        Assigned to: {{ $support->admin->name ?? '—' }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item view-btn" data-support='@json($support)'>
                                                <i class="bi bi-eye"></i> View
                                            </button>
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

        <div class="mt-3">{{ $supports->links() }}</div>
    </div>

    {{-- 🔹 Modal --}}
    <div class="modal fade" id="viewSupportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#4aa2f5; color:white;">
                    <h5 class="modal-title"><i class="bi bi-info-circle"></i> View Support Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="supportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="seller-tab" data-bs-toggle="tab" data-bs-target="#seller"
                                type="button">Seller Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="product-tab" data-bs-toggle="tab" data-bs-target="#product"
                                type="button">Product Details</button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="product-review-tab" data-bs-toggle="tab" data-bs-target="#review"
                                type="button">
                                Products Review
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages"
                                type="button">Messages</button>
                        </li>


                    </ul>

                    <div class="tab-content">
                        {{-- 🧍 Seller Info Tab --}}
                        <div class="tab-pane fade show active" id="seller" role="tabpanel">
                            <form id="sellerForm">
                                <div class="mb-3">
                                    <label class="form-label">Seller Name</label>
                                    <input type="text" class="form-control" id="sellerName" name="seller_name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Seller Email</label>
                                    <input type="email" class="form-control" id="sellerEmail" name="seller_email">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>

                                {{-- 🏦 Bank Info (Compact Style) --}}
                                <div class="card border-0 shadow-sm mt-3">
                                    <div
                                        class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="bi bi-bank me-2 text-primary"></i>Bank Information</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="editBankBtn">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>

                                    <div class="card-body py-2 px-3">
                                        {{-- View Mode --}}
                                        <div id="bankInfoView">
                                            <div class="row">
                                                <div class="col-md-6 small mb-1"><strong>Bank Name:</strong> <span
                                                        id="bankNameText" class="text-muted">—</span></div>
                                                <div class="col-md-6 small mb-1"><strong>Account No:</strong> <span
                                                        id="bankAccountText" class="text-muted">—</span></div>
                                                <div class="col-md-6 small mb-1"><strong>IFSC Code:</strong> <span
                                                        id="ifscCodeText" class="text-muted">—</span></div>
                                                <div class="col-md-6 small mb-1"><strong>UPI ID:</strong> <span
                                                        id="upiIdText" class="text-muted">—</span></div>
                                            </div>
                                        </div>

                                        {{-- Edit Mode --}}
                                        <div id="bankInfoEdit" class="d-none">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="bankNameInput" placeholder="Bank Name">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="bankAccountInput" placeholder="Account No">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="ifscCodeInput" placeholder="IFSC Code">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm" id="upiIdInput"
                                                        placeholder="UPI ID">
                                                </div>
                                            </div>
                                            <div class="text-end mt-2">
                                                <button type="button" class="btn btn-sm btn-success" id="saveBankBtn">
                                                    <i class="bi bi-check2"></i> Save
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    id="cancelBankBtn">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="product" role="tabpanel">
                            <form id="productForm">
                                <div class="mb-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="productName" name="product_name" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Product Image & Description</label>
                                    <div class="d-flex align-items-start gap-3 p-2 border rounded">
                                        <!-- 🖼 Product Image -->
                                        <img id="productImage" src="{{ asset('storage/images/no-image.png') }}" width="1000"
                                            height="100" class="rounded border shadow-sm">

                                        <!-- 📝 Product Description (read-only) -->
                                        <div class="flex-grow-1">
                                            <p id="productDescription" class="mb-0 text-muted" name="description"
                                                style="white-space: pre-line; line-height: 1.5;">
                                                No description available.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden placeholders so JS doesn’t break --}}
                                <textarea id="supportMessage" class="d-none"></textarea>
                                <button type="button" class="d-none"></button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="messages" role="tabpanel">
                            <div class="p-3">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-chat-dots"></i>
                                    Ticket Conversation
                                    <span id="modalTicketId" class="badge bg-primary ms-2"></span>
                                    <span id="modalTicketStatus" class="badge bg-secondary ms-2"></span>
                                </h6>

                                <!-- Messages list -->
                                <div id="messageList" class="border rounded p-3 mb-3"
                                    style="height: 300px; overflow-y: auto; background-color: #f9fafb;">
                                    <p class="text-muted text-center">No messages yet.</p>
                                </div>

                                <!-- 💬 Support Chat Message Form -->
                                <form id="messageForm" class="d-flex flex-column gap-2" enctype="multipart/form-data">
                                    <input type="hidden" id="ticketId" value="{{ $support->id ?? '' }}">

                                    <!-- 🔒 Locked preview area for attached documents -->
                                    <div id="attachmentPreviewArea" class="mb-2 d-flex flex-wrap gap-2"></div>

                                    <!-- 📝 Hidden input + Trix editor -->
                                    <input id="messageInput" type="hidden" name="message">
                                    <trix-editor input="messageInput" class="trix-content border rounded p-2 bg-white"
                                        placeholder="Type your message...">
                                    </trix-editor>

                                    <!-- 🚀 Send button -->
                                    <button type="submit" class="btn btn-primary align-self-end">
                                        <i class="bi bi-send"></i> Send
                                    </button>
                                </form>

                                <!-- 🖼️ Attachment Preview Modal -->
                                <div class="modal fade" id="attachmentPreviewModal" tabindex="-1"
                                    aria-labelledby="previewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="previewModalLabel">Attachment Preview</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center" id="previewContent">
                                                <p class="text-muted">Loading preview...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 🎫 Ticket Status Buttons -->
                                <div class="mb-3 d-flex justify-content-start align-items-center gap-2">
                                    <label class="fw-semibold me-2">Status:</label>
                                    <div class="btn-group" role="group" aria-label="Ticket Status">
                                        <button type="button" class="btn btn-outline-success btn-sm status-btn"
                                            data-status="open">
                                            <i class="bi bi-unlock"></i> Open
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm status-btn"
                                            data-status="pending">
                                            <i class="bi bi-hourglass-split"></i> Pending
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm status-btn"
                                            data-status="closed">
                                            <i class="bi bi-lock-fill"></i> Closed
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm status-btn"
                                            data-status="reopened"> <i class="bi bi-arrow-repeat"></i> Reopen</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Product Review Tab --}}
                        <div class="tab-pane fade" id="review" role="tabpanel">
                            <div class="p-3">
                                <h6 class="fw-bold mb-3">Product Reviews</h6>

                                {{-- 🔍 Search --}}
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="reviewSearch" class="form-control"
                                        placeholder="Search by customer or review...">
                                    <button type="button" class="btn btn-outline-secondary" id="reviewSearchBtn">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>

                                {{-- 🧾 Reviews List --}}
                                <div id="reviewList">
                                    <p class="text-muted text-center">No reviews available.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/seller-support.js') }}"></script>
@endpush