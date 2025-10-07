/* resources/js/category-index.js */
/* Requires Bootstrap loaded in layout (Bootstrap 5). */
// (() => {
//     document.addEventListener('DOMContentLoaded', () => {
//         const toastContainer = document.querySelector('.toast-container') || createToastContainer();

//         // Create toast container if missing
//         function createToastContainer() {
//             const t = document.createElement('div');
//             t.className = 'toast-container position-fixed top-0 end-0 p-3';
//             t.style.zIndex = 1055;
//             document.body.appendChild(t);
//             return t;
//         }

//         // showToast(message, type, timeout)
//         function showToast(message = '', type = 'info', timeout = 4000) {
//             if (!message) return;
//             const colorClass = (type === 'danger' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'primary');
//             const wrapper = document.createElement('div');
//             wrapper.className = `toast align-items-center text-bg-${colorClass} border-0`;
//             wrapper.role = 'alert';
//             wrapper.setAttribute('aria-live', 'polite');
//             wrapper.setAttribute('aria-atomic', 'true');

//             wrapper.innerHTML = `
//                 <div class="d-flex">
//                 <div class="toast-body">${message}</div>
//                 <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
//                 </div>
//             `;

//             toastContainer.appendChild(wrapper);
//             const bs = new bootstrap.Toast(wrapper, { delay: timeout });
//             bs.show();
//             wrapper.addEventListener('hidden.bs.toast', () => wrapper.remove());
//             return bs;
//         }

//         // Show server flash messages (set via window.__FLASH)
//         try {
//             if (window.__FLASH) {
//                 if (window.__FLASH.success) showToast(window.__FLASH.success, 'success');
//                 if (window.__FLASH.error) showToast(window.__FLASH.error, 'danger');
//             }
//         } catch (err) { /* ignore */ }

//         // ---------- Tree toggler ----------
//         document.querySelectorAll('.tree-toggler').forEach(t => {
//             t.addEventListener('click', e => {
//                 e.preventDefault();
//                 const parent = t.closest('.tree-item');
//                 if (!parent) return;
//                 const nested = parent.querySelector('.nested');
//                 if (!nested) return;
//                 const expanded = t.getAttribute('aria-expanded') === 'true';
//                 t.setAttribute('aria-expanded', !expanded);
//                 t.classList.toggle('bi-chevron-right', expanded);
//                 t.classList.toggle('bi-chevron-down', !expanded);
//                 nested.classList.toggle('active');
//             });
//         });

//         // ---------- Unified modal add/edit ----------
//         const actionModalEl = document.getElementById('actionModal');
//         const actionModal = actionModalEl ? new bootstrap.Modal(actionModalEl) : null;
//         const modalForm = document.getElementById('actionForm');
//         const modalTitle = document.getElementById('actionModalLabel');
//         const nameInput = document.getElementById('actionName');
//         const typeInput = document.getElementById('actionType');
//         const parentIdInput = document.getElementById('actionParentId');
//         const methodFieldContainer = document.getElementById('method-field');
//         const submitButton = document.getElementById('actionSubmitButton');

//         document.querySelectorAll('.action-btn').forEach(btn => {
//             btn.addEventListener('click', function () {
//                 const action = this.dataset.action;
//                 const type = this.dataset.type;
//                 if (!modalForm) return;
//                 modalForm.reset();
//                 methodFieldContainer.innerHTML = '';
//                 parentIdInput.value = '';
//                 submitButton.disabled = false;
//                 submitButton.innerHTML = 'Save';
//                 typeInput.value = type || '';

//                 if (action === 'add') {
//                     modalForm.action = window.appRoutes.storeCategory;
//                     modalTitle.textContent = `Add New ${capitalize(type)}`;
//                     if (type !== 'master') {
//                         modalTitle.textContent += ` to "${this.dataset.parentName || ''}"`;
//                     }
//                     parentIdInput.value = this.dataset.parentId || '';
//                     submitButton.innerHTML = 'Create';
//                 } else if (action === 'edit') {
//                     const id = this.dataset.id;
//                     modalForm.action = `/admin/categories/${id}`;
//                     // Use normal input for method override
//                     methodFieldContainer.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
//                     modalTitle.textContent = `Edit ${capitalize(type)}`;
//                     nameInput.value = this.dataset.name || '';
//                     submitButton.innerHTML = 'Save Changes';
//                 }
//                 if (actionModal) actionModal.show();
//             });
//         });

//         if (actionModalEl) actionModalEl.addEventListener('shown.bs.modal', () => {
//             const focusEl = document.getElementById('actionName');
//             if (focusEl) focusEl.focus();
//         });

//         // Prevent double submission for modal form
//         if (modalForm) {
//             modalForm.addEventListener('submit', function (ev) {
//                 const btn = modalForm.querySelector('button[type="submit"]');
//                 if (btn.disabled) {
//                     ev.preventDefault(); return;
//                 }
//                 btn.disabled = true;
//                 btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
//                 showToast('Processing request... Please wait.', 'info', 10000);
//             });
//         }

//         // ---------- Checkbox propagation (parent -> children) ----------
//         document.querySelectorAll('.parent-checkbox').forEach(parentCheckbox => {
//             parentCheckbox.addEventListener('change', function () {
//                 const item = this.closest('.tree-item');
//                 const nested = item ? item.querySelector('.nested') : null;
//                 if (!nested) return;
//                 nested.querySelectorAll('.child-checkbox').forEach(c => c.checked = this.checked);
//             });
//         });

//         // ---------- Image upload handler (AJAX w/ progress) ----------
//         document.querySelectorAll('.image-input').forEach(input => attachImageHandler(input));

//         function attachImageHandler(input) {
//             input.addEventListener('change', function () {
//                 const file = this.files && this.files[0];
//                 if (!file) return;
//                 const id = this.dataset.id;
//                 const type = this.dataset.type;
//                 const progressEl = document.getElementById(`progress-${id}`);
//                 const previewEl = document.querySelector(`.preview-${id}`);

//                 // disable the input while uploading
//                 input.disabled = true;

//                 const formData = new FormData();
//                 formData.append('image', file);
//                 formData.append('id', id);
//                 formData.append('type', type);
//                 formData.append('_token', document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : document.querySelector('input[name="_token"]').value);

//                 const xhr = new XMLHttpRequest();
//                 xhr.open('POST', window.appRoutes.uploadImage, true);

//                 xhr.upload.addEventListener('progress', e => {
//                     if (e.lengthComputable && progressEl) {
//                         const pct = Math.round((e.loaded / e.total) * 100);
//                         progressEl.style.display = 'flex';
//                         progressEl.textContent = pct + '%';
//                     }
//                 });

//                 xhr.onload = () => {
//                     if (progressEl) { progressEl.style.display = 'none'; progressEl.textContent = '0%'; }
//                     input.disabled = false;
//                     if (xhr.status === 200) {
//                         try {
//                             const res = JSON.parse(xhr.responseText);
//                             if (res.success) {
//                                 // cache-bust
//                                 if (previewEl) previewEl.src = res.url + '?v=' + Date.now();
//                                 showToast(res.message || 'Image uploaded', 'success');
//                             } else {
//                                 showToast(res.message || 'Upload failed', 'danger');
//                             }
//                         } catch (err) {
//                             showToast('Server returned invalid response', 'danger');
//                         }
//                     } else {
//                         showToast('Upload error', 'danger');
//                     }
//                 };

//                 xhr.onerror = () => {
//                     if (progressEl) { progressEl.style.display = 'none'; progressEl.textContent = '0%'; }
//                     input.disabled = false;
//                     showToast('Network error', 'danger');
//                 };

//                 xhr.send(formData);
//             });
//         }

//         // ---------- Bulk delete: modern confirm modal (lists names & consequences) ----------
//         const bulkForm = document.getElementById('bulkForm');
//         if (bulkForm) {
//             bulkForm.addEventListener('submit', function (ev) {
//                 ev.preventDefault();

//                 const checked = bulkForm.querySelectorAll('input[type="checkbox"]:checked');
//                 if (!checked.length) {
//                     showToast('Please select at least one item to delete.', 'warning'); return;
//                 }

//                 // collect selected items grouped by type
//                 const grouped = { master: [], section: [], category: [] };
//                 checked.forEach(ch => {
//                     const name = guessNameForCheckbox(ch);
//                     // determine type by name attribute pattern
//                     const nm = ch.getAttribute('name') || '';
//                     if (nm.includes('selected[master]')) grouped.master.push({ id: ch.value, name });
//                     else if (nm.includes('selected[section]')) grouped.section.push({ id: ch.value, name });
//                     else if (nm.includes('selected[category]')) grouped.category.push({ id: ch.value, name });
//                 });

//                 // build human readable message
//                 const lines = [];
//                 if (grouped.master.length) {
//                     if (grouped.master.length === 1) lines.push(`Master category "${escapeHtml(grouped.master[0].name)}" will be deleted along with its sections & categories.`);
//                     else lines.push(`The following master categories will be deleted (each removes its sections & categories): ${grouped.master.map(m => `"${escapeHtml(m.name)}"`).join(', ')}.`);
//                 }
//                 if (grouped.section.length) {
//                     if (grouped.section.length === 1) lines.push(`Section "${escapeHtml(grouped.section[0].name)}" will be deleted along with its categories.`);
//                     else lines.push(`The following sections will be deleted (each removes its categories): ${grouped.section.map(s => `"${escapeHtml(s.name)}"`).join(', ')}.`);
//                 }
//                 if (grouped.category.length) {
//                     if (grouped.category.length === 1) lines.push(`Category "${escapeHtml(grouped.category[0].name)}" will be deleted.`);
//                     else {
//                         const maxToShow = 6;
//                         const names = grouped.category.map(c => `"${escapeHtml(c.name)}"`);
//                         const show = names.slice(0, maxToShow).join(', ');
//                         lines.push(`The following ${grouped.category.length} categories will be deleted: ${show}${grouped.category.length > maxToShow ? ', ...' : ''}.`);
//                     }
//                 }
//                 lines.push('This action cannot be undone.');

//                 // show confirm modal
//                 showBulkConfirmModal(lines.join('<br>'), () => {
//                     // on confirm -> submit original form
//                     // disable submit buttons and show processing toast
//                     document.querySelectorAll('button, input[type="submit"]').forEach(b => b.disabled = true);
//                     showToast('Deleting selected items... Please wait.', 'info', 10000);

//                     // ensure hidden action field exists
//                     let hiddenAction = bulkForm.querySelector('input[name="action"]');
//                     if (!hiddenAction) {
//                         hiddenAction = document.createElement('input');
//                         hiddenAction.type = 'hidden';
//                         hiddenAction.name = 'action';
//                         bulkForm.appendChild(hiddenAction);
//                     }
//                     hiddenAction.value = 'delete';

//                     bulkForm.submit();
//                 });
//             });
//         }

//         // builds & shows bootstrap modal for bulk confirm. Calls onConfirm() if Delete clicked.
//         function showBulkConfirmModal(htmlMessage, onConfirm) {
//             // remove any existing temp modal
//             const existing = document.getElementById('bulkConfirmModal-temp');
//             if (existing) existing.remove();

//             const wrapper = document.createElement('div');
//             wrapper.id = 'bulkConfirmModal-temp';
//             wrapper.className = 'modal fade';
//             wrapper.innerHTML = `
//         <div class="modal-dialog modal-dialog-centered">
//           <div class="modal-content">
//             <div class="modal-header">
//               <h5 class="modal-title">Confirm Deletion</h5>
//               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
//             </div>
//             <div class="modal-body">${htmlMessage}</div>
//             <div class="modal-footer p-2">
//               <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
//               <button id="bulk-confirm-delete" type="button" class="btn btn-sm btn-danger">Delete</button>
//             </div>
//           </div>
//         </div>`;
//             document.body.appendChild(wrapper);
//             const bsModal = new bootstrap.Modal(wrapper);
//             bsModal.show();

//             wrapper.querySelector('#bulk-confirm-delete').addEventListener('click', () => {
//                 bsModal.hide();
//                 // small timeout to allow hide animation
//                 setTimeout(() => {
//                     if (typeof onConfirm === 'function') onConfirm();
//                     wrapper.remove();
//                 }, 210);
//             });

//             wrapper.addEventListener('hidden.bs.modal', () => wrapper.remove());
//         }

//         // helper: guess the user-visible name for a checkbox element
//         function guessNameForCheckbox(checkbox) {
//             try {
//                 const nameAttr = checkbox.getAttribute('name') || '';
//                 if (nameAttr.includes('selected[master]')) {
//                     const treeItem = checkbox.closest('.tree-item');
//                     const nameEl = treeItem ? treeItem.querySelector('.item-name') : null;
//                     return (nameEl && nameEl.textContent.trim()) || checkbox.value;
//                 }
//                 if (nameAttr.includes('selected[section]')) {
//                     const treeItem = checkbox.closest('.tree-item');
//                     const nameEl = treeItem ? treeItem.querySelector('.item-name') : null;
//                     return (nameEl && nameEl.textContent.trim()) || checkbox.value;
//                 }
//                 if (nameAttr.includes('selected[category]')) {
//                     const card = checkbox.closest('.card');
//                     const title = card ? card.querySelector('.card-title') : null;
//                     return (title && title.textContent.trim()) || checkbox.value;
//                 }
//             } catch (err) { /* ignore */ }
//             return checkbox.value || 'selected item';
//         }

//         // small helper to avoid injection in modal markup
//         function escapeHtml(s) {
//             return String(s).replace(/[&<>"'`=\/]/g, function (c) {
//                 return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' }[c];
//             });
//         }

//         // capitalize utility
//         function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }
//     });
// })();

/* resources/js/category-index.js */
/* Requires Bootstrap 5 loaded in layout */
(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();

        // ---------- Toast utility ----------
        function createToastContainer() {
            const t = document.createElement('div');
            t.className = 'toast-container position-fixed top-0 end-0 p-3';
            t.style.zIndex = 1055;
            document.body.appendChild(t);
            return t;
        }

        function showToast(message = '', type = 'info', timeout = 4000) {
            if (!message) return;
            const colorClass = type === 'danger' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'primary';
            const wrapper = document.createElement('div');
            wrapper.className = `toast align-items-center text-bg-${colorClass} border-0`;
            wrapper.role = 'alert';
            wrapper.setAttribute('aria-live', 'polite');
            wrapper.setAttribute('aria-atomic', 'true');

            wrapper.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            toastContainer.appendChild(wrapper);
            const bs = new bootstrap.Toast(wrapper, { delay: timeout });
            bs.show();
            wrapper.addEventListener('hidden.bs.toast', () => wrapper.remove());
            return bs;
        }

        // Show server flash messages (window.__FLASH)
        try {
            if (window.__FLASH) {
                if (window.__FLASH.success) showToast(window.__FLASH.success, 'success');
                if (window.__FLASH.error) showToast(window.__FLASH.error, 'danger');
            }
        } catch (err) { }

        // ---------- Tree toggler ----------
        document.querySelectorAll('.tree-toggler').forEach(t => {
            t.addEventListener('click', e => {
                e.preventDefault();
                const parent = t.closest('.tree-item');
                if (!parent) return;
                const nested = parent.querySelector('.nested');
                if (!nested) return;
                const expanded = t.getAttribute('aria-expanded') === 'true';
                t.setAttribute('aria-expanded', !expanded);
                t.classList.toggle('bi-chevron-right', expanded);
                t.classList.toggle('bi-chevron-down', !expanded);
                nested.classList.toggle('active');
            });
        });

        // ---------- Modal add/edit ----------
        const actionModalEl = document.getElementById('actionModal');
        const actionModal = actionModalEl ? new bootstrap.Modal(actionModalEl) : null;
        const modalForm = document.getElementById('actionForm');
        const modalTitle = document.getElementById('actionModalLabel');
        const nameInput = document.getElementById('actionName');
        const typeInput = document.getElementById('actionType');
        const parentIdInput = document.getElementById('actionParentId');
        const masterId = document.getElementById('actionMasterId');
        const methodFieldContainer = document.getElementById('method-field');
        const submitButton = document.getElementById('actionSubmitButton');

        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const action = this.dataset.action;
                const type = this.dataset.type;
                if (!modalForm) return;
                modalForm.reset();
                methodFieldContainer.innerHTML = '';
                parentIdInput.value = '';
                submitButton.disabled = false;
                submitButton.dataset.originalText = 'Save';
                submitButton.innerHTML = submitButton.dataset.originalText;
                typeInput.value = type || '';

                if (action === 'add') {
                    modalForm.action = window.appRoutes.storeCategory;
                    modalTitle.textContent = `Add New ${capitalize(type)}`;
                    if (type !== 'master') modalTitle.textContent += ` to "${this.dataset.parentName || ''}"`;
                    parentIdInput.value = this.dataset.parentId || '';
                    masterId.value = this.dataset.masterId || '';
                    submitButton.innerHTML = 'Create';
                    submitButton.dataset.originalText = 'Create';
                } else if (action === 'edit') {
                    const id = this.dataset.id;
                    modalForm.action = `/admin/categories/${id}`;
                    methodFieldContainer.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
                    modalTitle.textContent = `Edit ${capitalize(type)}`;
                    nameInput.value = this.dataset.name || '';
                    submitButton.innerHTML = 'Save Changes';
                    submitButton.dataset.originalText = 'Save Changes';
                }

                if (actionModal) actionModal.show();
            });
        });

        if (actionModalEl) actionModalEl.addEventListener('shown.bs.modal', () => {
            const focusEl = document.getElementById('actionName');
            if (focusEl) focusEl.focus();
        });

        // ---------- AJAX form submit ----------
        if (modalForm) {
            modalForm.addEventListener('submit', function (ev) {
                ev.preventDefault();
                const btn = modalForm.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;

                const formData = new FormData(modalForm);
                fetch(modalForm.action, {
                    method: formData.get('_method') || 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Saved successfully', 'success');
                            setTimeout(() => location.reload(), 500); // reload DOM or update dynamically
                            if (actionModal) actionModal.hide();
                        } else {
                            showToast(data.message || 'Operation failed', 'danger');
                        }
                    })
                    .catch(err => showToast('Server error', 'danger'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = submitButton.dataset.originalText;
                    });
            });
        }

        // ---------- Checkbox propagation ----------
        document.querySelectorAll('.parent-checkbox').forEach(parentCheckbox => {
            parentCheckbox.addEventListener('change', function () {
                const item = this.closest('.tree-item');
                const nested = item ? item.querySelector('.nested') : null;
                if (!nested) return;
                nested.querySelectorAll('.child-checkbox').forEach(c => c.checked = this.checked);
            });
        });

        // ---------- Image upload handler ----------
        document.querySelectorAll('.image-input').forEach(input => attachImageHandler(input));
        function attachImageHandler(input) {
            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                const id = this.dataset.id;
                const type = this.dataset.type;
                const progressEl = document.getElementById(`progress-${id}`);
                const previewEl = document.querySelector(`.preview-${id}`);

                input.disabled = true;
                const formData = new FormData();
                formData.append('image', file);
                formData.append('id', id);
                formData.append('type', type);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                const xhr = new XMLHttpRequest();
                xhr.open('POST', window.appRoutes.uploadImage, true);

                xhr.upload.addEventListener('progress', e => {
                    if (e.lengthComputable && progressEl) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        progressEl.style.display = 'flex';
                        progressEl.textContent = pct + '%';
                    }
                });

                xhr.onload = () => {
                    if (progressEl) { progressEl.style.display = 'none'; progressEl.textContent = '0%'; }
                    input.disabled = false;
                    if (xhr.status === 200) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.success) {
                                if (previewEl) previewEl.src = res.url + '?v=' + Date.now();
                                showToast(res.message || 'Image uploaded', 'success');
                            } else {
                                showToast(res.message || 'Upload failed', 'danger');
                            }
                        } catch (err) {
                            showToast('Server returned invalid response', 'danger');
                        }
                    } else showToast('Upload error', 'danger');
                };

                xhr.onerror = () => {
                    if (progressEl) { progressEl.style.display = 'none'; progressEl.textContent = '0%'; }
                    input.disabled = false;
                    showToast('Network error', 'danger');
                };

                xhr.send(formData);
            });
        }

        // ---------- AJAX Bulk Delete ----------
        const bulkForm = document.getElementById('bulkForm');
        if (bulkForm) {
            bulkForm.addEventListener('submit', function (ev) {
                ev.preventDefault();
                const checked = bulkForm.querySelectorAll('input[type="checkbox"]:checked');
                if (!checked.length) return showToast('Select at least one item', 'warning');

                const grouped = { master: [], section: [], category: [] };
                checked.forEach(ch => {
                    const name = guessNameForCheckbox(ch);
                    const nm = ch.getAttribute('name') || '';
                    if (nm.includes('selected[master]')) grouped.master.push({ id: ch.value, name });
                    else if (nm.includes('selected[section]')) grouped.section.push({ id: ch.value, name });
                    else if (nm.includes('selected[category]')) grouped.category.push({ id: ch.value, name });
                });

                const lines = [];
                if (grouped.master.length) lines.push(grouped.master.length === 1 ?
                    `Master category "${escapeHtml(grouped.master[0].name)}" will be deleted along with sections & categories.` :
                    `Master categories to be deleted: ${grouped.master.map(m => `"${escapeHtml(m.name)}"`).join(', ')}.`);
                if (grouped.section.length) lines.push(grouped.section.length === 1 ?
                    `Section "${escapeHtml(grouped.section[0].name)}" will be deleted along with categories.` :
                    `Sections to be deleted: ${grouped.section.map(s => `"${escapeHtml(s.name)}"`).join(', ')}.`);
                if (grouped.category.length) {
                    const maxToShow = 6;
                    const names = grouped.category.map(c => `"${escapeHtml(c.name)}"`);
                    const show = names.slice(0, maxToShow).join(', ');
                    lines.push(`Categories to be deleted: ${show}${grouped.category.length > maxToShow ? ', ...' : ''}.`);
                }
                lines.push('This action cannot be undone.');

                showBulkConfirmModal(lines.join('<br>'), () => {
                    const formData = new FormData(bulkForm);
                    formData.append('action', 'delete');

                    fetch(window.appRoutes.bulkAction, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showToast(data.message || 'Deleted successfully', 'success');
                                setTimeout(() => location.reload(), 500);
                            } else {
                                showToast(data.message || 'Delete failed', 'danger');
                            }
                        })
                        .catch(err => showToast('Server error', 'danger'));
                });
            });
        }

        function showBulkConfirmModal(htmlMessage, onConfirm) {
            const existing = document.getElementById('bulkConfirmModal-temp'); if (existing) existing.remove();
            const wrapper = document.createElement('div');
            wrapper.id = 'bulkConfirmModal-temp'; wrapper.className = 'modal fade';
            wrapper.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">${htmlMessage}</div>
                        <div class="modal-footer p-2">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button id="bulk-confirm-delete" type="button" class="btn btn-sm btn-danger">Delete</button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(wrapper);
            const bsModal = new bootstrap.Modal(wrapper); bsModal.show();
            wrapper.querySelector('#bulk-confirm-delete').addEventListener('click', () => {
                bsModal.hide();
                setTimeout(() => { if (onConfirm) onConfirm(); wrapper.remove(); }, 210);
            });
            wrapper.addEventListener('hidden.bs.modal', () => wrapper.remove());
        }

        function guessNameForCheckbox(checkbox) {
            try {
                const nameAttr = checkbox.getAttribute('name') || '';
                if (nameAttr.includes('selected[master]') || nameAttr.includes('selected[section]')) {
                    const treeItem = checkbox.closest('.tree-item');
                    const nameEl = treeItem ? treeItem.querySelector('.item-name') : null;
                    return (nameEl && nameEl.textContent.trim()) || checkbox.value;
                }
                if (nameAttr.includes('selected[category]')) {
                    const card = checkbox.closest('.card');
                    const title = card ? card.querySelector('.card-title') : null;
                    return (title && title.textContent.trim()) || checkbox.value;
                }
            } catch (err) { }
            return checkbox.value || 'selected item';
        }

        function escapeHtml(s) {
            return String(s).replace(/[&<>"'`=\/]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' }[c]));
        }

        function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }
    });
})();
