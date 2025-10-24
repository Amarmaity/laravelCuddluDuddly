$(function () {
    // ✅ Initialize Select2
    $('#categorySelect').select2({
        placeholder: "Search and select categories",
        width: '100%',
    });

    const dropZone = $('#dropZone')[0],
        dropzoneContent = $('#dropzoneContent')[0],
        previews = $('#imagePreviews')[0],
        input = $('#imageInput')[0],
        progressBox = $('#uploadProgress')[0],
        bar = progressBox.querySelector('.progress-bar'),
        count = progressBox.querySelector('.upload-count'),
        total = progressBox.querySelector('.total-count'),
        errorBox = $('#errorMessage')[0];

    let allFiles = [], uploading = false;

    // 🔸 Unified error handler
    const showError = (msg, type = 'danger', duration = 4000) => {
        errorBox.innerHTML = `<div class="alert alert-${type} py-2 mb-0">${msg}</div>`;
        errorBox.classList.remove('d-none');
        setTimeout(() => {
            errorBox.classList.add('fade');
            errorBox.style.opacity = '0';
            setTimeout(() => {
                errorBox.classList.add('d-none');
                errorBox.innerHTML = '';
                errorBox.classList.remove('fade');
                errorBox.style.opacity = '1';
            }, 400);
        }, duration);
    };

    // 🔹 Handle files
    const handleFiles = files => {
        if (uploading) return showError('Please wait for current upload to complete');
        errorBox.classList.add('d-none');

        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'],
            allowedExts = ['jpg', 'jpeg', 'png'];
        let valid = [], large = 0, wrongType = 0;

        [...files].forEach(f => {
            const ext = f.name.split('.').pop().toLowerCase(),
                mime = f.type.toLowerCase();

            if (!allowedTypes.includes(mime) && !allowedExts.includes(ext)) return wrongType++;
            if (f.size > 512000) return large++;
            if (!allFiles.some(x => x.name === f.name && x.size === f.size)) valid.push(f);
        });

        if (wrongType) showError(`${wrongType} file(s) not added — only JPG, JPEG, PNG allowed.`, 'warning');
        if (large) showError(`${large} image(s) exceeded 500KB.`, 'warning');
        if (!valid.length) return;

        allFiles.push(...valid);
        updatePreviews();
        simulateUpload();
    };

    // 🖼️ Update image previews
    const updatePreviews = () => {
        previews.innerHTML = '';
        allFiles.forEach((f, i) => {
            const r = new FileReader();
            r.onload = e => {
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="preview-remove" data-index="${i}">
                        <i class="bi bi-trash-fill"></i>
                    </button>`;
                previews.appendChild(item);
            };
            r.readAsDataURL(f);
        });
        previews.classList.toggle('d-none', !allFiles.length);
        dropZone.classList.toggle('has-images', !!allFiles.length);
    };

    // ⏳ Simulated upload progress
    const simulateUpload = () => {
        if (!allFiles.length) return;
        uploading = true;
        progressBox.classList.remove('d-none');
        bar.style.width = '0%';
        count.textContent = '0';
        total.textContent = allFiles.length;
        let uploaded = 0, totalFiles = allFiles.length;

        const timer = setInterval(() => {
            uploaded++;
            const pct = (uploaded / totalFiles) * 100;
            bar.style.width = `${pct}%`;
            count.textContent = uploaded;
            if (uploaded === totalFiles) {
                clearInterval(timer);
                setTimeout(() => {
                    progressBox.classList.add('d-none');
                    uploading = false;
                }, 700);
            }
        }, 300);
    };

    // 🗑️ Remove preview with fade
    previews.addEventListener('click', e => {
        const btn = e.target.closest('.preview-remove');
        if (!btn) return;
        if (uploading) return showError('Please wait until upload completes');
        const idx = +btn.dataset.index,
            item = btn.closest('.preview-item');
        item.classList.add('fade-out');
        setTimeout(() => {
            allFiles.splice(idx, 1);
            updatePreviews();
        }, 200);
    });

    // 🖱️ Click or Drop
    dropZone.addEventListener('click', e => !e.target.closest('.preview-remove') && input.click());
    ['dragover', 'dragleave', 'drop'].forEach(evt =>
        dropZone.addEventListener(evt, e => {
            e.preventDefault();
            if (evt === 'dragover') dropZone.classList.add('dragover');
            else if (evt === 'dragleave') dropZone.classList.remove('dragover');
            else {
                dropZone.classList.remove('dragover');
                handleFiles(e.dataTransfer.files);
            }
        })
    );
    input.addEventListener('change', () => handleFiles(input.files));

    // ✅ Validate before submit + prevent multi-click
    $('#productForm').on('submit', function (e) {
        const submitBtn = $(this).find('button[type="submit"]');

        // Prevent double click
        if (submitBtn.prop('disabled')) {
            e.preventDefault();
            return;
        }

        // Validation
        if (allFiles.length < 3) {
            e.preventDefault();
            showError('At least 3 images are required.');
            return;
        }
        if (uploading) {
            e.preventDefault();
            showError('Please wait until upload completes.', 'info');
            return;
        }

        // Disable button + show loader
        submitBtn.prop('disabled', true);
        submitBtn.data('original-text', submitBtn.html());
        submitBtn.html(`<span class="spinner-border spinner-border-sm me-2"></span>Uploading...`);

        // Attach files
        const dt = new DataTransfer();
        allFiles.forEach(f => dt.items.add(f));
        input.files = dt.files;
    });
});
