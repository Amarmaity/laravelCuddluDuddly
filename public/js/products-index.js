document.addEventListener('DOMContentLoaded', () => {
    const selectAll = $('#selectAll');
    const toastContainer = $('.toast-container');
    const deleteModal = new bootstrap.Modal($('#confirmDeleteModal')[0]);
    let pendingDelete = null;

    // ✅ Select All
    selectAll.on('change', function () {
        $('.product-checkbox').prop('checked', this.checked);
    });

    const getSelected = () => $('.product-checkbox:checked').map((_, el) => el.value).get();

    // ✅ Toast
    const showToast = (msg, type = 'success') => {
        if (!toastContainer.length) return;
        const toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0 show shadow-sm position-relative" role="alert">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">${msg}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `).hide().appendTo(toastContainer).fadeIn(150);
        setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2000);
    };

    // ✅ Spinner toggle
    const toggleBtn = (btn, load = true, text = '') => {
        if (!btn) return;
        const $btn = $(btn);
        if (load) {
            $btn.data('text', $btn.html())
                .html(`<span class="spinner-border spinner-border-sm me-2"></span>${text || 'Processing...'}`)
                .prop('disabled', true);
        } else {
            $btn.html($btn.data('text')).prop('disabled', false);
        }
    };

    // ✅ Core Bulk/Single Action
    window.handleBulkAction = async (url, method, action, onRow, ids = null, srcBtn = null) => {
        const selected = ids || getSelected();
        if (!selected.length) return showToast('Please select at least one product.', 'danger');
        const btn = srcBtn || $(`#${action.toLowerCase()}Selected`);
        toggleBtn(btn, true, `${action}ing...`);

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                body: JSON.stringify({ ids: selected }),
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message || `${action} successful`);
                selected.forEach(id => {
                    const row = $(`.product-checkbox[value="${id}"]`).closest('tr')[0];
                    row && onRow(row);
                });
                if (!ids) $('.product-checkbox, #selectAll').prop('checked', false);
            } else showToast(data.message || 'Something went wrong.', 'danger');
        } catch {
            showToast('Server error. Please try again.', 'danger');
        } finally {
            toggleBtn(btn, false);
        }
    };

    // 🔘 Define common badge toggles
    const toggleBadge = (row, cell, yesTxt, yesCls, noTxt, noCls) => {
        const badge = row.querySelector(`td:nth-child(${cell}) .badge`);
        const isYes = badge.textContent.trim() === yesTxt;
        badge.className = `badge ${isYes ? noCls : yesCls}`;
        badge.textContent = isYes ? noTxt : yesTxt;
    };

    // 🌟 Bulk Feature
    $('#featureSelected').on('click', () =>
        handleBulkAction('products/bulk-feature', 'POST', 'Feature',
            row => toggleBadge(row, 8, 'Yes', 'bg-warning text-dark', 'No', 'bg-secondary'))
    );

    // ✅ Bulk Approve
    $('#approveSelected').on('click', () =>
        handleBulkAction('products/bulk-approve', 'POST', 'Approve',
            row => toggleBadge(row, 9, 'Approved', 'bg-success', 'Pending', 'bg-danger'))
    );

    // 🗑️ Bulk Delete (with modal confirm)
    $('#deleteSelected').on('click', () => {
        const ids = getSelected();
        if (!ids.length) return showToast('Please select at least one product.', 'danger');
        pendingDelete = () =>
            handleBulkAction('products/destroy', 'DELETE', 'Delete', row => $(row).fadeOut(300, () => row.remove()));
        deleteModal.show();
    });

    $('#confirmDeleteBtn').on('click', () => {
        if (pendingDelete) pendingDelete();
        deleteModal.hide();
    });

    // 🔹 Individual Actions
    const handleIndividual = (selector, url, action, cell, yesTxt, yesCls, noTxt, noCls) => {
        $(document).on('click', selector, function () {
            const id = $(this).data('id');
            const btn = this;
            handleBulkAction(url, 'POST', action, row => toggleBadge(row, cell, yesTxt, yesCls, noTxt, noCls), [id], btn);
            $('#productViewModal').modal('hide');
        });
    };

    handleIndividual('.btn-feature', 'products/bulk-feature', 'Feature', 8, 'Yes', 'bg-warning text-dark', 'No', 'bg-secondary');
    handleIndividual('.btn-approve', 'products/bulk-approve', 'Approve', 9, 'Approved', 'bg-success', 'Pending', 'bg-danger');

    // 🗑️ Individual Delete
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        const btn = this;
        pendingDelete = () =>
            handleBulkAction('products/destroy', 'DELETE', 'Delete', row => $(row).fadeOut(300, () => row.remove()), [id], btn);
        $('#productViewModal').modal('hide');
        setTimeout(() => deleteModal.show(), 300);
    });
});

// 🔍 Quick View Modal
$(document).on('click', '.btn-view-product', function () {
    const id = $(this).data('id');
    const modal = new bootstrap.Modal($('#productViewModal')[0]);
    const content = $('#productViewContent');
    content.html(`
        <div class="text-center text-muted py-4">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 mb-0 small">Loading product details...</p>
        </div>
    `);
    modal.show();

    $.get(`products/${id}/quick-view`)
        .done(res => content.html(res.html))
        .fail(() => content.html('<p class="text-danger text-center py-4">Failed to load product details.</p>'));
});
