@extends('admin.layouts.admin')

@section('title', 'Add Seller')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3"><i class="bi bi-plus-circle me-2"></i> Add New Seller</h5>

            <form action="{{ route('admin.sellers.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                {{-- Business Info --}}
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-shop"></i> Business Name <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Contact Person --}}
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-badge"></i> Contact Person <span
                            class="text-danger">*</span></label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                        class="form-control @error('contact_person') is-invalid @enderror" required>
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email & Phone --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Email <span
                                class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-telephone"></i> Phone <span
                                class="text-danger">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="form-control @error('phone') is-invalid @enderror" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Address --}}
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt"></i> Address</label>
                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}"
                            class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" id="state" name="state" value="{{ old('state') }}"
                            class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" id="country" name="country" value="{{ old('country', 'India') }}"
                            class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                        class="form-control">
                    <div id="postal-feedback" class="text-muted small"></div>
                </div>

                {{-- Tax Details --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GST Number</label>
                        <input type="text" id="gst_number" name="gst_number" value="{{ old('gst_number') }}"
                            class="form-control">
                        <div id="gst-feedback" class="text-muted small"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">PAN Number</label>
                        <input type="text" id="pan_number" name="pan_number" value="{{ old('pan_number') }}"
                            class="form-control">
                        <div id="pan-feedback" class="text-muted small"></div>
                    </div>
                </div>

                {{-- Bank Info --}}
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-bank"></i> Bank Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}"
                        class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name') }}"
                            class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code') }}"
                            class="form-control">
                        <div id="ifsc-feedback" class="text-muted small"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">UPI ID</label>
                        <input type="text" id="upi_id" name="upi_id" value="{{ old('upi_id') }}"
                            class="form-control">
                        <div id="ifsc-feedback" class="text-muted small"></div>
                    </div>
                    <div class="col-md-6 mb-3"> <label class="form-label">Commission Rate (%)</label> <input
                            type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate') }}"
                            class="form-control @error('commission_rate') is-invalid @enderror"> @error('commission_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div> {{-- Uploads --}}
                <div class="row">
                    <div class="col-md-6 mb-3"> <label class="form-label">Seller Logo</label> <input type="file"
                            name="logo" class="form-control @error('logo') is-invalid @enderror"> @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3"> <label class="form-label">Documents (KYC, Agreement, etc.)</label> <input
                            type="file" name="documents[]" multiple
                            class="form-control @error('documents') is-invalid @enderror"> @error('documents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- Status --}}
                <div class="form-check mb-3"> <input type="checkbox" name="is_active" value="1"
                        class="form-check-input" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}> <label
                        class="form-check-label" for="is_active">
                        Active Seller</label> </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.sellers.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-circle"></i> Save Seller
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {

            document.querySelector('[name="postal_code"]').addEventListener('blur', function() {
                let pin = this.value.trim();
                if (pin.length === 6) {
                    fetch(`https://api.postalpincode.in/pincode/${pin}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data[0].Status === "Success") {
                                let post = data[0].PostOffice[0];
                                document.querySelector('[name="city"]').value = post.District;
                                document.querySelector('[name="state"]').value = post.State;
                                document.querySelector('[name="country"]').value = "India";
                            }
                        });
                }
            });

            // 2. GST Validation
            document.getElementById("gst_number").addEventListener("blur", function() {
                let gst = this.value.trim();
                if (gst.length === 15) {
                    document.getElementById("gst-feedback").innerHTML =
                        `<span class="text-info">Checking GST...</span>`;
                    // TODO: Replace with real API endpoint + auth headers
                    fetch(`/api/validate/gst/${gst}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.valid) {
                                document.getElementById("gst-feedback").innerHTML =
                                    `<span class="text-success"><i class="bi bi-check-circle"></i> GST Valid: ${data.legal_name}</span>`;
                            } else {
                                document.getElementById("gst-feedback").innerHTML =
                                    `<span class="text-danger"><i class="bi bi-x-circle"></i> ${data.message}</span>`;
                            }
                        });
                }
            });

            // 3. PAN Validation
            document.getElementById("pan_number").addEventListener("blur", function() {
                let pan = this.value.trim();
                if (/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(pan)) {
                    document.getElementById("pan-feedback").innerHTML =
                        `<span class="text-info">Checking PAN...</span>`;
                    // TODO: Replace with real PAN validation API
                    fetch(`/api/validate/pan/${pan}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.valid) {
                                document.getElementById("pan-feedback").innerHTML =
                                    `<span class="text-success"><i class="bi bi-check-circle"></i> PAN Valid: ${data.holder}</span>`;
                            } else {
                                document.getElementById("pan-feedback").innerHTML =
                                    `<span class="text-danger"><i class="bi bi-x-circle"></i> ${data.message}</span>`;
                            }
                        });
                } else {
                    document.getElementById("pan-feedback").innerHTML =
                        `<span class="text-danger"><i class="bi bi-x-circle"></i> Invalid PAN Format</span>`;
                }
            });

            document.querySelector('[name="ifsc_code"]').addEventListener('blur', function() {
                let ifsc = this.value.trim();
                if (ifsc.length === 11) {
                    fetch(`https://ifsc.razorpay.com/${ifsc}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.BANK) {
                                document.querySelector('[name="bank_name"]').value = data.BANK;
                            }
                        })
                        .catch(() => alert("Invalid IFSC Code"));
                }
            });
        });
    </script> --}}
@endpush
