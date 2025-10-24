@php
    // If $address not passed, set default empty array to avoid errors
    $address =
        $address ??
        (object) [
            'shipping_name' => '',
            'shipping_phone' => '',
            'shipping_email' => '',
            'landmark' => '',
            'address_line1' => '',
            'address_line2' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => 'India',
        ];
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="shipping_name" class="form-control form-control-sm shadow-sm"
            value="{{ old('shipping_name', $address->shipping_name) }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
        <input type="text" name="shipping_phone" class="form-control form-control-sm shadow-sm"
            value="{{ old('shipping_phone', $address->shipping_phone) }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="shipping_email" class="form-control form-control-sm shadow-sm"
            value="{{ old('shipping_email', $address->shipping_email) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Address Line 1 <span class="text-danger">*</span></label>
        <input type="text" name="address_line1" class="form-control form-control-sm shadow-sm"
            value="{{ old('address_line1', $address->address_line1) }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Address Line 2</label>
        <input type="text" name="address_line2" class="form-control form-control-sm shadow-sm"
            value="{{ old('address_line2', $address->address_line2) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Landmark</label>
        <input type="text" name="landmark" class="form-control form-control-sm shadow-sm"
            value="{{ old('landmark', $address->landmark) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
        <input type="text" name="city" class="form-control form-control-sm shadow-sm"
            value="{{ old('city', $address->city) }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
        <input type="text" name="state" class="form-control form-control-sm shadow-sm"
            value="{{ old('state', $address->state) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-semibold">Postal Code <span class="text-danger">*</span></label>
        <input type="text" name="postal_code" class="form-control form-control-sm shadow-sm"
            value="{{ old('postal_code', $address->postal_code) }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Country</label>
        <input type="text" name="country" class="form-control form-control-sm shadow-sm"
            value="{{ old('country', $address->country ?? 'India') }}">
    </div>
</div>
