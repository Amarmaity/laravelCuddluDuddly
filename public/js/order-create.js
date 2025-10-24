/**
 * order-create.js
 * Handles dynamic behaviors for order creation page:
 * - Customer address loading
 * - Edit/Delete address actions
 * - Manual address toggle & reset
 * - Dynamic order item rows
 * - Safe form submission with loader
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ========== DOM ELEMENTS ========== */
    const customerSelect = document.querySelector('select[name="user_id"]');
    const addressContainer = document.getElementById('existing-addresses');
    const newAddressForm = document.getElementById('new-address-form');
    const toggleButton = document.getElementById('toggle-new-address');
    const orderForm = document.getElementById('orderCreateForm');
    const createBtn = document.getElementById('createOrderBtn');
    const orderItemsBody = document.getElementById('order-items-body');

    /* ========== LOAD ADDRESSES ========== */
    customerSelect.addEventListener('change', function () {
        const userId = this.value;
        addressContainer.innerHTML = '<div class="text-muted small">Loading addresses...</div>';
        newAddressForm.style.display = 'none';

        if (!userId) {
            addressContainer.innerHTML =
                '<div class="text-muted small">Select a customer to view saved addresses...</div>';
            return;
        }

        fetch(`/admin/orders/get-addresses/${userId}`)
            .then(res => res.json())
            .then(addresses => {
                if (addresses.length === 0) {
                    addressContainer.innerHTML = `
                        <div class="alert alert-info small mb-0">
                            No saved addresses found. You can add a new one below.
                        </div>`;
                    return;
                }

                let html = '<div class="row g-3">';
                addresses.forEach((addr) => {
                    const checked = addr.is_default ? 'checked' : '';
                    html += `
                    <div class="col-auto mb-3">
                        <div class="card address-card shadow-sm border-0 rounded-3 position-relative ${addr.is_default ? 'border-primary border-2' : ''}" 
                            style="min-width: 260px; height: 220px; cursor: pointer;">
                            
                            <div class="card-body p-3 h-100 d-flex flex-column justify-content-between">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input me-2" type="radio" name="selected_address_id"
                                                    value="${addr.id}" id="addr${addr.id}" ${checked}>
                                                <label class="form-check-label fw-semibold text-dark" for="addr${addr.id}">
                                                    ${addr.shipping_name}
                                                </label>
                                                <div class="text-muted small">(${addr.shipping_phone})</div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-light border edit-address px-1 py-0" 
                                                data-id="${addr.id}" title="Edit">
                                                <i class="bi bi-pencil text-primary"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light border delete-address px-1 py-0" 
                                                data-id="${addr.id}" title="Delete">
                                                <i class="bi bi-trash3 text-danger"></i>
                                            </button>
                                        </div>
                                    </div>

                                    ${addr.shipping_email ? `<div class="text-muted small mb-1"><i class="bi bi-envelope me-1"></i>${addr.shipping_email}</div>` : ''}
                                    
                                    <div class="small">
                                        <div>${addr.address_line1}${addr.address_line2 ? ', ' + addr.address_line2 : ''}</div>
                                        <div>${addr.city}, ${addr.state} - ${addr.postal_code}</div>
                                        <div class="text-muted">${addr.country}</div>
                                    </div>
                                  ${addr.landmark ? `<div class="text-muted small mt-2"><i class="bi bi-geo-alt me-1"></i>${addr.landmark}</div>` : ''}
                            </div>
                        </div>
                    </div>`;
                });

                html += '</div>';
                addressContainer.innerHTML = html;
            })
            .catch(() => {
                addressContainer.innerHTML =
                    '<div class="text-danger small">Failed to load addresses.</div>';
            });
    });

    /* ========== TOGGLE NEW ADDRESS FORM ========== */
    toggleButton.addEventListener('click', () => {
        newAddressForm.style.display =
            newAddressForm.style.display === 'none' ? 'block' : 'none';
    });

    /* ========== EDIT & DELETE ADDRESS ACTIONS ========== */
    document.addEventListener('click', async (e) => {
        const editBtn = e.target.closest('.edit-address');
        const delBtn = e.target.closest('.delete-address');

        // --- Edit address ---
        if (editBtn) {
            const id = editBtn.dataset.id;
            try {
                const res = await fetch(`/admin/shipping-addresses/${id}`);
                const addr = await res.json();

                document.getElementById('edit_address_id').value = addr.id;
                document.getElementById('edit_shipping_name').value = addr.shipping_name;
                document.getElementById('edit_shipping_phone').value = addr.shipping_phone;
                document.getElementById('edit_shipping_email').value = addr.shipping_email || '';
                document.getElementById('edit_landmark').value = addr.landmark || '';
                document.getElementById('edit_address_line1').value = addr.address_line1;
                document.getElementById('edit_address_line2').value = addr.address_line2 || '';
                document.getElementById('edit_city').value = addr.city;
                document.getElementById('edit_state').value = addr.state;
                document.getElementById('edit_postal_code').value = addr.postal_code;

                new bootstrap.Modal(document.getElementById('editAddressModal')).show();
            } catch {
                alert('Failed to load address details.');
            }
        }

        // --- Save edited address ---
        if (e.target.id === 'save-address-changes') {
            const btn = e.target;
            btn.disabled = true;
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

            const form = document.getElementById('edit-address-form');
            const id = document.getElementById('edit_address_id').value;
            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/shipping-addresses/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': formData.get('_token') },
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editAddressModal')).hide();
                    customerSelect.dispatchEvent(new Event('change'));
                    showToast('Address updated successfully!', 'success');
                } else {
                    alert('Update failed.');
                }
            } catch {
                alert('Network error.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        }

        // --- Delete address ---
        if (delBtn) {
            const id = delBtn.dataset.id;
            if (!confirm('Are you sure you want to delete this address?')) return;

            const res = await fetch(`/admin/shipping-addresses/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const data = await res.json();

            if (data.success) customerSelect.dispatchEvent(new Event('change'));
            else alert('Failed to delete address.');
        }
    });

    /* ========== MUTUAL EXCLUSIVITY: RADIO <-> MANUAL FORM ========== */
    document.addEventListener('change', (e) => {
        if (e.target.name === 'selected_address_id') {
            // Hide manual form & reset it when existing address selected
            newAddressForm.style.display = 'none';
            newAddressForm.querySelectorAll('input').forEach(inp => inp.value = '');
        }

        if (e.target.closest('#new-address-form')) {
            // Uncheck all radios when typing manually
            document.querySelectorAll('input[name="selected_address_id"]').forEach(r => r.checked = false);
        }
    });

    /* ========== ORDER ITEMS DYNAMIC ROWS ========== */
    document.addEventListener('click', (e) => {
        // Add item
        if (e.target.closest('.add-item')) {
            const firstRow = orderItemsBody.querySelector('tr.order-item');
            const clone = firstRow.cloneNode(true);
            clone.querySelector('select').selectedIndex = 0;
            clone.querySelector('input').value = 1;

            clone.querySelector('td:last-child').innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger remove-item rounded-circle">
                    <i class="bi bi-trash3"></i>
                </button>`;
            orderItemsBody.appendChild(clone);
        }

        // Remove item
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr.order-item').remove();
        }
    });

    /* ========== FORM SUBMIT PREVENT MULTI-CLICK ========== */
    if (orderForm) {
        orderForm.addEventListener('submit', (e) => {
            if (!createBtn) return; // extra safety

            if (createBtn.disabled) {
                e.preventDefault();
                return;
            }
            createBtn.disabled = true;
            createBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Creating...`;
        });
    }

    /* ========== SMALL TOAST UTILITY ========== */
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = 1100;
        toast.innerHTML = `
            <div class="toast align-items-center text-bg-${type} border-0 show shadow">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
});