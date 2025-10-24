<div class="mb-3">
    <label for="name" class="form-label fw-semibold">Brand Name <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" value="{{ old('name', $brand->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="logo" class="form-label fw-semibold">Logo</label>
    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror">
    @error('logo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if (!empty($brand->logo))
        <img src="{{ asset('storage/' . $brand->logo) }}" alt="Logo" class="mt-2"
            style="width:80px; height:80px; object-fit:cover;">
    @endif
</div>

<div class="mb-3">
    <label for="description" class="form-label fw-semibold">Description</label>
    <textarea name="description" id="description" rows="3"
        class="form-control @error('description') is-invalid @enderror">{{ old('description', $brand->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" id="is_active"
        class="form-check-input @error('is_active') is-invalid @enderror" value="1"
        {{ old('is_active', $brand->is_active ?? 1) ? 'checked' : '' }}>
    <label for="is_active" class="form-check-label">Active</label>
    @error('is_active')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="text-end">
    <button type="submit" class="btn btn-primary rounded-pill px-4 submit-btn">
        <i class="bi bi-check-circle me-1"></i> {{ $submit ?? 'Save' }}
    </button>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('.submit-btn');
                    if (submitBtn) {
                        // Prevent multiple clicks
                        submitBtn.disabled = true;

                        // Change text to loader text
                        let originalText = submitBtn.innerHTML;

                        // Determine text based on action
                        let actionText = '{{ isset($brand) ? 'Updating...' : 'Creating...' }}';

                        submitBtn.innerHTML =
                            `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${actionText}`;
                    }
                });
            });
        });
    </script>
@endpush
