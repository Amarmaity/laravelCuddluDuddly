{{-- @extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-cash-coin me-2"></i> Initiate Payout</h4>
            <a href="{{ route('admin.payouts.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <form id="payoutForm" method="POST" action="{{ route('admin.payouts.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row g-4">

                        <!-- Seller -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Seller <span class="text-danger">*</span></label>
                            <select id="sellerSelect" name="seller_id" class="form-select" required>
                                <option value="">-- Choose seller --</option>
                                @foreach ($sellers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mode -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payout Mode <span class="text-danger">*</span></label>
                            <select id="modeSelect" name="mode" class="form-select" required>
                                <option value="" selected disabled>-- Select mode --</option>
                                <option value="IMPS">IMPS (Instant)</option>
                                <option value="NEFT">NEFT (Standard)</option>
                                <option value="RTGS">RTGS (High Value)</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" name="amount" class="form-control"
                                    placeholder="Enter amount" required>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Optional note">
                        </div>

                        <!-- Mode-specific extra fields -->
                        <div class="col-12" id="bankFields" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Bank Account No. <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="accountField" class="form-control" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">IFSC Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="ifscField" class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="upiFields" style="display:none;">
                            <label class="form-label fw-semibold">UPI ID <span class="text-danger">*</span></label>
                            <input type="text" id="upiField" class="form-control" disabled>
                        </div>

                        <!-- Beneficiary Preview -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Beneficiary Details</label>
                            <div id="beneficiaryPreview" class="border rounded-3 p-3 bg-light small">
                                <em class="text-muted">Select a seller to preview saved bank/UPI details.</em>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer bg-white text-end border-0">
                    <button type="button" id="previewBtn" class="btn btn-primary" disabled>
                        <i class="bi bi-eye me-1"></i> Preview & Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-sm rounded-3">
                <form id="confirmForm">
                    <div class="modal-header bg-light border-0 py-3">
                        <h6 class="modal-title fw-semibold mb-0">
                            <i class="bi bi-shield-check me-2 text-success"></i> Confirm Payout
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body small">
                        <div id="confirmBody" class="mb-3"></div>

                        <div class="alert alert-warning py-2 px-3 mb-3 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Please verify all details carefully before proceeding.
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ackVerify" required>
                            <label for="ackVerify" class="form-check-label">
                                I confirm beneficiary details and payout amount are correct.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check2-circle me-1"></i> Confirm & Initiate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const sellerSelect = document.getElementById('sellerSelect');
                const modeSelect = document.getElementById('modeSelect');
                const beneficiaryPreview = document.getElementById('beneficiaryPreview');
                const previewBtn = document.getElementById('previewBtn');

                const bankFields = document.getElementById('bankFields');
                const upiFields = document.getElementById('upiFields');
                const accountField = document.getElementById('accountField');
                const ifscField = document.getElementById('ifscField');
                const upiField = document.getElementById('upiField');

                let beneficiaryData = {};

                // fetch beneficiary on seller change
                sellerSelect.addEventListener('change', async () => {
                    const id = sellerSelect.value;
                    if (!id) {
                        beneficiaryPreview.innerHTML =
                            '<em class="text-muted">Select a seller to preview saved bank/UPI details.</em>';
                        previewBtn.disabled = true;
                        return;
                    }

                    const resp = await fetch("{{ url('admin/sellers/bank-details') }}/" + id);
                    if (!resp.ok) {
                        beneficiaryPreview.innerHTML =
                            '<div class="text-danger">Unable to fetch details.</div>';
                        previewBtn.disabled = true;
                        return;
                    }

                    const data = await resp.json();
                    if (!data || !data.success) {
                        beneficiaryPreview.innerHTML =
                            '<div class="text-warning">No bank details available or not verified.</div>';
                        previewBtn.disabled = true;
                        return;
                    }

                    beneficiaryData = data.data;
                    beneficiaryPreview.innerHTML = `
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${beneficiaryData.name}</strong><br>
                                <small class="text-muted">${beneficiaryData.email ?? ''}</small>
                            </div>
                            <span class="badge bg-${beneficiaryData.verified ? 'success' : 'secondary'}">
                                ${beneficiaryData.verified ? 'Verified' : 'Not Verified'}
                            </span>
                        </div>
                        <div class="text-muted small">
                            <div><strong>Account:</strong> ${beneficiaryData.account ?? '-'}</div>
                            <div><strong>IFSC:</strong> ${beneficiaryData.ifsc ?? '-'}</div>
                            <div><strong>UPI:</strong> ${beneficiaryData.upi ?? '-'}</div>
                        </div>
                    `;
                    previewBtn.disabled = !beneficiaryData.verified;
                });

                // mode toggle fields
                modeSelect.addEventListener('change', () => {
                    bankFields.style.display = "none";
                    upiFields.style.display = "none";

                    if (modeSelect.value === 'UPI') {
                        upiFields.style.display = "block";
                        upiField.value = beneficiaryData.upi ?? '';
                    } else if (['IMPS', 'NEFT', 'RTGS'].includes(modeSelect.value)) {
                        bankFields.style.display = "block";
                        accountField.value = beneficiaryData.account ?? '';
                        ifscField.value = beneficiaryData.ifsc ?? '';
                    }
                });

                // preview modal
                previewBtn.addEventListener('click', () => {
                    const content = beneficiaryPreview.innerHTML;
                    const confirmBody = document.getElementById('confirmBody');
                    confirmBody.innerHTML = `
                        <div class="border rounded-3 p-3 bg-light">
                            ${content}
                            <hr class="my-2">
                            <div><strong>Mode:</strong> ${modeSelect.value}</div>
                            <div><strong>Payout Amount:</strong> ₹${document.querySelector('input[name="amount"]').value || '0.00'}</div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('confirmModal')).show();
                });

                document.getElementById('confirmForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    document.getElementById('payoutForm').submit();
                });
            });
        </script>
    @endpush
@endsection --}}



@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-cash-coin me-2"></i> Initiate Payout</h4>
            <a href="{{ route('admin.payouts.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Back
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <form id="payoutForm" method="POST" action="{{ route('admin.payouts.store') }}"
                data-bank-url="{{ url('admin/sellers/bank-details') }}">
                @csrf
                <div class="card-body">
                    <div class="row g-4">

                        <!-- Seller -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Seller <span class="text-danger">*</span></label>
                            <select id="sellerSelect" name="seller_id" class="form-select" required>
                                <option value="">-- Choose seller --</option>
                                @foreach ($sellers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mode -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payout Mode <span class="text-danger">*</span></label>
                            <select id="modeSelect" name="mode" class="form-select" required>
                                <option value="" selected disabled>-- Select mode --</option>
                                <option value="IMPS">IMPS (Instant)</option>
                                <option value="NEFT">NEFT (Standard)</option>
                                <option value="RTGS">RTGS (High Value)</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" name="amount" class="form-control"
                                    placeholder="Enter amount" required>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Optional note">
                        </div>

                        <!-- Mode-specific fields -->
                        <div class="col-12" id="bankFields" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Bank Account No. <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="accountField" class="form-control" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">IFSC Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="ifscField" class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="upiFields" style="display:none;">
                            <label class="form-label fw-semibold">UPI ID <span class="text-danger">*</span></label>
                            <input type="text" id="upiField" class="form-control" disabled>
                        </div>

                        <!-- Beneficiary Preview -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Beneficiary Details</label>
                            <div id="beneficiaryPreview" class="border rounded-3 p-3 bg-light small">
                                <em class="text-muted">Select a seller to preview saved bank/UPI details.</em>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer bg-white text-end border-0">
                    <button type="button" id="previewBtn" class="btn btn-primary" disabled>
                        <i class="bi bi-eye me-1"></i> Preview & Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-sm rounded-3">
                <form id="confirmForm">
                    <div class="modal-header bg-light border-0 py-3">
                        <h6 class="modal-title fw-semibold mb-0">
                            <i class="bi bi-shield-check me-2 text-success"></i> Confirm Payout
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body small">
                        <div id="confirmBody" class="mb-3"></div>

                        <div class="alert alert-warning py-2 px-3 mb-3 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Please verify all details carefully before proceeding.
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ackVerify" required>
                            <label for="ackVerify" class="form-check-label">
                                I confirm beneficiary details and payout amount are correct.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" id="confirmSubmitBtn" class="btn btn-success btn-sm">
                            <span class="btn-text"><i class="bi bi-check2-circle me-1"></i> Confirm & Initiate</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/payout.js') }}"></script>
@endpush
