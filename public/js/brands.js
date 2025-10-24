document.addEventListener('DOMContentLoaded', function () {
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this brand?')) {
                e.preventDefault();
            }
        });
    });

    // Logo preview
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const preview = document.createElement('img');
            preview.style.width = '80px';
            preview.style.height = '80px';
            preview.style.objectFit = 'cover';
            preview.classList.add('mt-2');

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                if (logoInput.nextElementSibling?.tagName === 'IMG') {
                    logoInput.parentNode.removeChild(logoInput.nextElementSibling);
                }
                logoInput.parentNode.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
    }
});
