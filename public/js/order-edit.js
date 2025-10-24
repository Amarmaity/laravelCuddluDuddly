/**
 * order-edit.js
 * Enhanced version:
 * - Dynamic address handling with order_status-based warnings & restrictions
 * - Editable or restricted address modes
 * - Functional add/remove order item rows
 */

document.addEventListener('DOMContentLoaded', () => {
    const customerSelect = document.querySelector('select[name="user_id"]');
    const addressContainer = document.getElementById('existing-addresses');
    const newAddressForm = document.getElementById('new-address-form');
    const toggleButton = document.getElementById('toggle-new-address');
    const orderStatusSelect = document.querySelector('select[name="order_status"]');
    const orderItemsBody = document.getElementById('order-items-body');

    const currentOrderStatus = orderStatusSelect.value;
    // const preselectedCustomer = document.getElementById('preselectedUserId')?.value || customerSelect.value;
    // const preselectedAddress = document.getElementById('preselectedAddressId')?.value || null;
    const preselectedCustomer = window.orderData?.userId || customerSelect.value;
    const preselectedAddress = window.orderData?.shippingAddressId || null;

    console.log(preselectedAddress);
    // ====== Utility: Toast (optional UX) ======
    const showToast = (msg, type = 'success') => {
        const div = document.createElement('div');
        div.className = `alert alert-${type} position-fixed top-0 end-0 mt-3 me-3 shadow`;
        div.style.zIndex = 2000;
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    };

    // ====== Load addresses with restrictions/warnings ======
    const loadAddresses = (userId, selectedAddressId = null) => {
        addressContainer.innerHTML = '<div class="text-muted small">Loading addresses...</div>';
        newAddressForm.style.display = 'none';

        if (!userId) {
            addressContainer.innerHTML = '<div class="text-muted small">Select a customer to view saved addresses...</div>';
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

                let html = '';

                if (['pending', 'processing'].includes(currentOrderStatus)) {
                    html += `
                <div class="alert alert-warning small py-1 px-2 mb-0 rounded-2">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Changing the shipping address may cause a slight delivery delay.
                </div>`;
                } else if (currentOrderStatus === 'shipped') {
                    html += `
                    <div class="alert alert-warning small py-1 px-2 mb-0 rounded-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        This address can’t be changed after shipment, but you can 
                        <a href="#" class="text-primary text-decoration-underline fw-semibold request-change-link">
                            request an update
                        </a> if needed.
                    </div>`;
                }


                html += '<div class="row g-3">';
                addresses.forEach((addr) => {
                    const checked = selectedAddressId && addr.id == selectedAddressId ? 'checked' : (addr.is_default ? 'checked' : '');
                    const editable = ['pending', 'processing'].includes(currentOrderStatus);
                    html += `
                        <div class="col-auto mb-3">
                            <div class="card address-card shadow-sm border-0 rounded-3 position-relative ${checked ? 'border-primary border-2' : ''}" 
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

                                        ${editable ? `
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-light border edit-address px-1 py-0" 
                                                data-id="${addr.id}" title="Edit">
                                                <i class="bi bi-pencil text-primary"></i>
                                            </button>
                                        </div>` : ''}
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

                // Disable add new address for shipped orders
                if (currentOrderStatus === 'shipped') {
                    toggleButton.style.display = 'none';
                } else {
                    toggleButton.style.display = 'inline-block';
                }
            })
            .catch(() => {
                addressContainer.innerHTML = '<div class="text-danger small">Failed to load addresses.</div>';
            });
    };

    // ====== Toggle New Address ======
    toggleButton.addEventListener('click', () => {
        newAddressForm.style.display = newAddressForm.style.display === 'none' ? 'block' : 'none';
    });

    // ====== Load addresses immediately for edit mode ======
    if (preselectedCustomer) {
        loadAddresses(preselectedCustomer, preselectedAddress);
    }

    // ====== Listen for manual customer changes ======
    customerSelect.addEventListener('change', function () {
        loadAddresses(this.value);
    });

    // ====== Address card selection logic ======
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.address-card');
        if (card) {
            const radio = card.querySelector('input[name="selected_address_id"]');
            if (radio) {
                radio.checked = true;
                document.querySelectorAll('.address-card').forEach(c => c.classList.remove('border-primary', 'border-2'));
                card.classList.add('border-primary', 'border-2');
                newAddressForm.style.display = 'none';
                newAddressForm.querySelectorAll('input, textarea').forEach(inp => inp.value = '');
            }
        }
    });

    document.addEventListener('change', (e) => {
        if (e.target && e.target.name === 'selected_address_id') {
            const val = e.target.value;
            document.querySelectorAll('.address-card').forEach(c => {
                const r = c.querySelector('input[name="selected_address_id"]');
                if (r && r.value === val) c.classList.add('border-primary', 'border-2');
                else c.classList.remove('border-primary', 'border-2');
            });
            newAddressForm.style.display = 'none';
        }
    });

    // ====== Edit Address Modal Handling ======
    document.addEventListener('click', async (e) => {
        const editBtn = e.target.closest('.edit-address');

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
                    loadAddresses(customerSelect.value, preselectedAddress);
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

    // ====== ORDER ITEMS add/remove ======
    document.addEventListener('click', (e) => {
        // Add new row
        if (e.target.closest('.add-item')) {
            const lastRow = e.target.closest('tr');
            const clone = lastRow.cloneNode(true);
            clone.querySelector('select[name="product_id[]"]').value = '';
            clone.querySelector('input[name="quantity[]"]').value = 1;

            // Replace the + button with a remove button
            const actionCell = clone.querySelector('td.text-center');
            actionCell.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger remove-item rounded-circle">
                    <i class="bi bi-trash3"></i>
                </button>`;

            orderItemsBody.appendChild(clone);
        }

        // Remove row
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#order-items-body tr').length > 1) {
                row.remove();
            } else {
                showToast('At least one product is required!', 'warning');
            }
        }
    });
});
