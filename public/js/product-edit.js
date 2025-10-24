// product-edit.js (fixed: stable IDs + placeholder rendering -> no shuffle / wrong removals)
$(document).ready(function () {
    /* Initialize Select2 */
    $('#categorySelect').select2({
        placeholder: "Search and select categories",
        width: '100%',
    });

    // DOM refs
    const dropZone = document.getElementById('dropZone');
    const dropzoneContent = document.getElementById('dropzoneContent');
    const imagePreviews = document.getElementById('imagePreviews');
    const imageInput = document.getElementById('imageInput');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const uploadCount = uploadProgress.querySelector('.upload-count');
    const totalCount = uploadProgress.querySelector('.total-count');
    const errorMessage = document.getElementById('errorMessage');
    const removedImagesInput = document.getElementById('removedImages');

    // State
    let existingImages = []; // { id, src }
    let allFiles = [];       // { id, file }
    let removedImages = [];  // ids of existing images to delete
    let uploadInProgress = false;

    // small uid generator for file entries
    function uid() {
        return Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
    }

    // Initialize existingImages from server-rendered DOM and then clear DOM
    (function initExisting() {
        const existingNodes = document.querySelectorAll('.preview-item.existing');
        existingNodes.forEach(node => {
            const id = node.dataset.id;
            const img = node.querySelector('img');
            const src = img ? img.getAttribute('src') : '';
            if (id) existingImages.push({ id: id.toString(), src });
        });
        // clear server-rendered nodes; we'll render from arrays for consistent behavior
        imagePreviews.innerHTML = '';
        renderPreviews();
    })();

    // show error same style as create
    function showError(message, type = 'danger', duration = 4000) {
        errorMessage.innerHTML = `<div class="alert alert-${type} py-2 mb-0">${message}</div>`;
        errorMessage.classList.remove('d-none');
        errorMessage.style.opacity = '1';
        setTimeout(() => {
            errorMessage.classList.add('fade');
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.classList.add('d-none');
                errorMessage.innerHTML = '';
                errorMessage.classList.remove('fade');
                errorMessage.style.opacity = '1';
            }, 400);
        }, duration);
    }

    // sync removed images hidden input
    function syncRemovedInput() {
        removedImagesInput.value = removedImages.join(',');
    }

    // RENDER PREVIEWS: create wrappers in the same order as arrays, use placeholders for new files,
    // then fill img.src asynchronously when FileReader finishes.
    function renderPreviews() {
        imagePreviews.innerHTML = '';

        const existingToShow = existingImages.filter(img => !removedImages.includes(String(img.id)));
        const hasImages = (existingToShow.length + allFiles.length) > 0;

        // Toggle UI state (same as create)
        if (hasImages) {
            imagePreviews.classList.remove('d-none');
            dropZone.classList.add('has-images');
            if (dropzoneContent) {
                dropzoneContent.style.opacity = '0.15';
                dropzoneContent.style.pointerEvents = 'none';
            }
        } else {
            imagePreviews.classList.add('d-none');
            dropZone.classList.remove('has-images');
            if (dropzoneContent) {
                dropzoneContent.style.opacity = '1';
                dropzoneContent.style.pointerEvents = 'auto';
            }
        }

        // Append existing images first (synchronous)
        existingToShow.forEach(imgObj => {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-item existing';
            wrapper.dataset.id = String(imgObj.id);
            wrapper.innerHTML = `
                <img src="${imgObj.src}" alt="Existing Image">
                <button type="button" class="preview-remove" data-existing-id="${imgObj.id}" title="Remove">
                    <i class="bi bi-trash-fill"></i>
                </button>
            `;
            imagePreviews.appendChild(wrapper);
        });

        // Append placeholders for new files in the order of allFiles
        // For each fileEntry create wrapper + img (will fill src when reader finishes)
        allFiles.forEach((entry) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-item new';
            wrapper.dataset.fileId = entry.id;

            // Create image element but no src yet (placeholder)
            const imgEl = document.createElement('img');
            imgEl.alt = 'Preview';
            // keep dimensions via CSS, src set after read
            wrapper.appendChild(imgEl);

            // remove button (data-file-id for removal)
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'preview-remove';
            btn.setAttribute('data-file-id', entry.id);
            btn.title = 'Remove';
            btn.innerHTML = '<i class="bi bi-trash-fill"></i>';
            wrapper.appendChild(btn);

            imagePreviews.appendChild(wrapper);

            // Read file and populate image src WITHOUT changing wrapper order
            const reader = new FileReader();
            reader.onload = e => {
                imgEl.src = e.target.result;
            };
            reader.readAsDataURL(entry.file);
        });

        syncRemovedInput();
    }

    // Handle files (validate then add to allFiles as {id, file})
    function handleFiles(files) {
        if (!files || files.length === 0) return;
        if (uploadInProgress) {
            showError('Please wait for current upload to complete');
            return;
        }

        const validEntries = [];
        let rejectedType = 0;
        let rejectedLarge = 0;

        Array.from(files).forEach(file => {
            const mime = (file.type || '').toLowerCase();
            const ext = (file.name || '').split('.').pop().toLowerCase();
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const allowedExts = ['jpg', 'jpeg', 'png'];

            const isValidType = allowedTypes.includes(mime) || allowedExts.includes(ext);
            if (!isValidType) {
                rejectedType++;
                return;
            }

            // duplicate check among pending allFiles (by name+size)
            const isDuplicate = allFiles.some(entry => entry.file.name === file.name && entry.file.size === file.size);
            if (isDuplicate) return;

            if (file.size > 500 * 1024) {
                rejectedLarge++;
                return;
            }

            validEntries.push({ id: uid(), file });
        });

        if (rejectedType > 0) showError(`${rejectedType} file(s) were not added — only JPG, JPEG, and PNG are allowed.`, 'warning');
        if (rejectedLarge > 0) showError(`${rejectedLarge} image(s) exceeded 500 KB and were not added.`, 'warning');

        if (validEntries.length === 0) return;

        // push new entries preserving drop order
        allFiles = [...allFiles, ...validEntries];

        // Render placeholders + readers, then simulate upload progress (visual only)
        renderPreviews();
        simulateUploadProgress();
    }

    // Fake upload progress (visual only, does not clear previews)
    function simulateUploadProgress() {
        if (allFiles.length === 0) return;
        uploadInProgress = true;

        uploadProgress.classList.remove('d-none');
        progressBar.style.width = '0%';
        uploadCount.textContent = '0';
        totalCount.textContent = allFiles.length;

        let uploaded = 0;
        const total = allFiles.length;
        const interval = setInterval(() => {
            uploaded++;
            const percent = Math.min((uploaded / total) * 100, 100);
            progressBar.style.width = `${percent}%`;
            uploadCount.textContent = uploaded;

            if (uploaded >= total) {
                clearInterval(interval);
                setTimeout(() => {
                    uploadProgress.classList.add('d-none');
                    uploadInProgress = false;
                    // keep previews intact (we already rendered placeholders + filled them)
                }, 600);
            }
        }, 250);
    }

    // Click handler for remove buttons — works for existing (data-existing-id) and new (data-file-id)
    imagePreviews.addEventListener('click', function (e) {
        const btn = e.target.closest('.preview-remove');
        if (!btn) return;

        if (uploadInProgress) {
            showError('Please wait until upload finishes.', 'info');
            return;
        }

        const existingId = btn.getAttribute('data-existing-id');
        const fileId = btn.getAttribute('data-file-id');
        const previewItem = btn.closest('.preview-item');
        if (!previewItem) return;

        // Add fade-out for smooth UI
        previewItem.classList.add('fade-out');

        setTimeout(() => {
            // Existing images -> mark for deletion
            if (existingId) {
                if (!removedImages.includes(String(existingId))) removedImages.push(String(existingId));
                // remove DOM node
                previewItem.remove();
                // re-render existing + new to keep order consistent (not strictly necessary but safe)
                renderPreviews();
                syncRemovedInput();
                return;
            }

            // New-file removal -> remove by fileId (stable)
            if (fileId) {
                // remove DOM node
                previewItem.remove();
                // remove the file entry from allFiles by id
                allFiles = allFiles.filter(entry => entry.id !== fileId);
                // re-render to refresh dataset/file placeholders and consistent order
                renderPreviews();
                syncRemovedInput();
                return;
            }

            // fallback: just remove node
            previewItem.remove();
            if (imagePreviews.children.length === 0) {
                imagePreviews.classList.add('d-none');
                dropZone.classList.remove('has-images');
                if (dropzoneContent) {
                    dropzoneContent.style.opacity = '1';
                    dropzoneContent.style.pointerEvents = 'auto';
                }
            }
            syncRemovedInput();
        }, 160); // matches CSS fade timing
    });

    // Dropzone & input handlers (same semantics as create)
    dropZone.addEventListener('click', (e) => {
        if (!e.target.closest('.preview-remove')) {
            imageInput.click();
        }
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer && e.dataTransfer.files) {
            handleFiles(e.dataTransfer.files);
        }
    });

    imageInput.addEventListener('change', () => {
        handleFiles(imageInput.files);
        // reset input so same filename can be selected again if needed
        imageInput.value = '';
    });

    // 🧾 Submit: build DataTransfer from allFiles (extract .file)
    $('#productEditForm').on('submit', function (e) {
        const $btn = $(this).find('button[type="submit"]');
        const totalImages = (existingImages.length - removedImages.length) + allFiles.length;

        // 🔒 Prevent multiple clicks
        if ($btn.prop('disabled')) {
            e.preventDefault();
            return false;
        }

        // Disable the button & show loader
        $btn.prop('disabled', true).addClass('position-relative');
        const loader = $('<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>');
        $btn.append(loader);

        // 🧩 Validation
        if (totalImages < 3) {
            e.preventDefault();
            showError('At least 3 images are required.', 'danger');
            loader.remove();
            $btn.prop('disabled', false);
            return;
        }

        if (uploadInProgress) {
            e.preventDefault();
            showError('Please wait for upload to finish.', 'info');
            loader.remove();
            $btn.prop('disabled', false);
            return;
        }

        // ✅ Proceed if valid
        const dt = new DataTransfer();
        allFiles.forEach(entry => dt.items.add(entry.file));
        imageInput.files = dt.files;

        // sync removed
        syncRemovedInput();
    });
});
