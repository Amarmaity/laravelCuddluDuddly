document.addEventListener('DOMContentLoaded', () => {
    const payoutForm = document.getElementById('payoutForm');
    const bankUrl = payoutForm.dataset.bankUrl;

    const sellerSelect = document.getElementById('sellerSelect');
    const modeSelect = document.getElementById('modeSelect');
    const beneficiaryPreview = document.getElementById('beneficiaryPreview');
    const previewBtn = document.getElementById('previewBtn');

    const bankFields = document.getElementById('bankFields');
    const upiFields = document.getElementById('upiFields');
    const accountField = document.getElementById('accountField');
    const ifscField = document.getElementById('ifscField');
    const upiField = document.getElementById('upiField');

    let beneficiaryData = {};

    // fetch beneficiary on seller change
    sellerSelect.addEventListener('change', async () => {
        const id = sellerSelect.value;
        if (!id) {
            beneficiaryPreview.innerHTML =
                '<em class="text-muted">Select a seller to preview saved bank/UPI details.</em>';
            previewBtn.disabled = true;
            return;
        }

        try {
            const resp = await fetch(`${bankUrl}/${id}`);
            const data = await resp.json();

            if (!data || !data.success) {
                beneficiaryPreview.innerHTML =
                    '<div class="text-warning">No bank details available or not verified.</div>';
                previewBtn.disabled = true;
                return;
            }

            beneficiaryData = data.data;
            beneficiaryPreview.innerHTML = `
                <div class="mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${beneficiaryData.name}</strong><br>
                        <small class="text-muted">${beneficiaryData.email ?? ''}</small>
                    </div>
                    <span class="badge bg-${beneficiaryData.verified ? 'success' : 'secondary'}">
                        ${beneficiaryData.verified ? 'Verified' : 'Not Verified'}
                    </span>
                </div>
                <div class="text-muted small">
                    <div><strong>Account:</strong> ${beneficiaryData.account ?? '-'}</div>
                    <div><strong>IFSC:</strong> ${beneficiaryData.ifsc ?? '-'}</div>
                    <div><strong>UPI:</strong> ${beneficiaryData.upi ?? '-'}</div>
                </div>
            `;
            previewBtn.disabled = !beneficiaryData.verified;
        } catch (err) {
            beneficiaryPreview.innerHTML =
                '<div class="text-danger">Unable to fetch details.</div>';
            previewBtn.disabled = true;
        }
    });

    // mode toggle fields
    modeSelect.addEventListener('change', () => {
        bankFields.style.display = "none";
        upiFields.style.display = "none";

        if (modeSelect.value === 'UPI') {
            upiFields.style.display = "block";
            upiField.value = beneficiaryData.upi ?? '';
        } else if (['IMPS', 'NEFT', 'RTGS'].includes(modeSelect.value)) {
            bankFields.style.display = "block";
            accountField.value = beneficiaryData.account ?? '';
            ifscField.value = beneficiaryData.ifsc ?? '';
        }
    });

    // preview modal
    previewBtn.addEventListener('click', () => {
        const confirmBody = document.getElementById('confirmBody');
        confirmBody.innerHTML = `
            <div class="border rounded-3 p-3 bg-light">
                ${beneficiaryPreview.innerHTML}
                <hr class="my-2">
                <div><strong>Mode:</strong> ${modeSelect.value}</div>
                <div><strong>Payout Amount:</strong> ₹${document.querySelector('input[name="amount"]').value || '0.00'}</div>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('confirmModal')).show();
    });

    // confirm form submit with loader
    document.getElementById('confirmForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Processing...`;

        payoutForm.submit();
    });
});