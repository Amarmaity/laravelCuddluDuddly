<!-- resources/views/admin/partials/universal-delete-modal.blade.php -->
<div class="modal fade" id="universalDeleteModal" tabindex="-1" aria-labelledby="universalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="universalDeleteLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body" id="universalDeleteBody">
                Are you sure you want to delete this item? This action cannot be undone.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cancel
                </button>

                <form id="universalDeleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="ids" id="universalDeleteIds">
                    <button type="submit" id="universalDeleteConfirmBtn" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Elements
        const modalEl = document.getElementById('universalDeleteModal');
        const bsModal = new bootstrap.Modal(modalEl);
        const bodyEl = modalEl.querySelector('#universalDeleteBody');
        const formEl = modalEl.querySelector('#universalDeleteForm');
        const idsInput = modalEl.querySelector('#universalDeleteIds');
        const confirmBtn = modalEl.querySelector('#universalDeleteConfirmBtn');

        // current state
        let current = {
            url: '',
            ids: [],
            ajax: true,
            onSuccess: null
        };

        // helper: create bootstrap alert
        function showAlert(type, message) {
            const icon = (type === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
            const wrapper = document.createElement('div');
            wrapper.className = `alert alert-${type} alert-dismissible fade show shadow-sm mt-3`;
            wrapper.innerHTML = `
            <i class="bi ${icon} me-2"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            const container = document.querySelector('.container-fluid') || document.body;
            container.prepend(wrapper);
        }

        // public: openDeleteModal({url, ids, message, ajax=true, onSuccess})
        window.openDeleteModal = function(opts = {}) {
            current.url = opts.url || '';
            current.ids = Array.isArray(opts.ids) ? opts.ids : (opts.ids ? [opts.ids] : []);
            current.ajax = (typeof opts.ajax === 'boolean') ? opts.ajax : true;
            current.onSuccess = (typeof opts.onSuccess === 'function') ? opts.onSuccess : null;

            idsInput.value = JSON.stringify(current.ids);
            bodyEl.textContent = opts.message || (current.ids.length > 1 ?
                `Are you sure you want to delete ${current.ids.length} items? This action cannot be undone.` :
                'Are you sure you want to delete this item? This action cannot be undone.'
            );

            formEl.action = current.url || '';
            bsModal.show();
        };

        // Event-delegate: open modal for any element with .open-delete-modal
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.open-delete-modal');
            if (!btn) return;

            e.preventDefault();

            const url = btn.dataset.deleteUrl || btn.getAttribute('href') || '';
            let ids = null;

            const idsAttr = btn.dataset.deleteIds || btn.dataset.ids || btn.dataset.id || null;
            if (idsAttr) {
                try {
                    ids = JSON.parse(idsAttr);
                } catch (err) {
                    ids = (idsAttr.indexOf(',') !== -1) ? idsAttr.split(',').map(s => s.trim()) : [idsAttr];
                }
            }

            const message = btn.dataset.deleteMessage || btn.dataset.message || null;
            const ajax = (typeof btn.dataset.deleteAjax !== 'undefined') ? (btn.dataset.deleteAjax ===
                'true') : true;

            window.openDeleteModal({
                url,
                ids,
                message,
                ajax
            });
        });

        // Submit handler (AJAX by default, fallback to normal POST if ajax=false)
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();

            const urlToUse = current.url || formEl.action;
            if (!urlToUse) {
                bsModal.hide();
                return;
            }

            const ids = current.ids;

            // Non-AJAX fallback
            if (!current.ajax) {
                idsInput.value = JSON.stringify(ids);
                formEl.action = urlToUse;
                formEl.submit();
                return;
            }

            // AJAX delete (using FormData for POST)
            confirmBtn.disabled = true;

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                'content') || '');
            formData.append('ids', JSON.stringify(ids));

            fetch(urlToUse, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // ✅ ensures Laravel sees it as AJAX
                    },
                    body: formData
                })
                .then(async res => {
                    confirmBtn.disabled = false;
                    let data;
                    try {
                        data = await res.json();
                    } catch {
                        data = {
                            success: res.ok,
                            message: res.ok ? "Deleted successfully." :
                                "Invalid server response"
                        };
                    }
                    return data;
                })
                .then(data => {
                    bsModal.hide();

                    if (data && data.success) {
                        showAlert('success', data.message || 'Deleted successfully.');
                        ids.forEach(id => document.querySelector(`tr[data-id="${id}"]`)?.remove());
                        if (current.onSuccess) current.onSuccess(data);
                    } else {
                        showAlert('danger', data.message || 'Failed to delete.');
                    }
                })
                .catch(() => {
                    confirmBtn.disabled = false;
                    bsModal.hide();
                    showAlert('danger', 'Something went wrong. Try again.');
                });
        });
    })();
</script>