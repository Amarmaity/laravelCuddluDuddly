document.addEventListener("DOMContentLoaded", () => {

    // =====================================================
    // 🧍 VIEW SUPPORT DETAILS
    // =====================================================
    document.querySelectorAll(".view-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const support = JSON.parse(btn.dataset.support);

            // 🧍 Seller Info
            document.getElementById("sellerName").value = support.seller?.name || '';
            document.getElementById("sellerEmail").value = support.seller?.email || '';
            document.getElementById("phone").value = support.seller?.phone || '';

            // 🏦 Bank Info (display)
            document.getElementById("bankNameText").textContent = support.seller?.bank_name || '—';
            document.getElementById("bankAccountText").textContent = support.seller?.bank_account_number || '—';
            document.getElementById("ifscCodeText").textContent = support.seller?.ifsc_code || '—';
            document.getElementById("upiIdText").textContent = support.seller?.upi_id || '—';

            // 🏦 Bank Info (edit fields)
            document.getElementById("bankNameInput").value = support.seller?.bank_name || '';
            document.getElementById("bankAccountInput").value = support.seller?.bank_account_number || '';
            document.getElementById("ifscCodeInput").value = support.seller?.ifsc_code || '';
            document.getElementById("upiIdInput").value = support.seller?.upi_id || '';

            // Save seller ID for update
            document.getElementById("saveBankBtn").dataset.sellerId = support.seller?.id;

            // 📦 Product Info
            document.getElementById("productName").value = support.product?.name || '';
            document.getElementById("supportMessage").value = support.message || '';
            document.getElementById("productDescription").textContent =
                support.product?.description || 'No description available.';

            const imgPath = support.product?.primary_image?.image_path
                ? `/storage/images/${support.product.primary_image.image_path}`
                : '/images/no-image.png';

            document.getElementById("productImage").src = imgPath;

            const productId = support.product?.id;

            // 🔥 Show Modal
            new bootstrap.Modal(document.getElementById("viewSupportModal")).show();

            // 🧾 Load latest product review
            if (productId) loadProductReviews(productId);
        });
    });

    // =====================================================
    // 🏦 BANK INFO EDIT / SAVE / CANCEL
    // =====================================================
    const editBtn = document.getElementById('editBankBtn');
    const viewDiv = document.getElementById('bankInfoView');
    const editDiv = document.getElementById('bankInfoEdit');
    const saveBtn = document.getElementById('saveBankBtn');
    const cancelBtn = document.getElementById('cancelBankBtn');

    editBtn?.addEventListener('click', () => {
        viewDiv.classList.add('d-none');
        editDiv.classList.remove('d-none');
    });

    cancelBtn?.addEventListener('click', () => {
        editDiv.classList.add('d-none');
        viewDiv.classList.remove('d-none');
    });

    // 💾 SAVE BANK INFO
    saveBtn?.addEventListener('click', async () => {
        const sellerId = saveBtn.dataset.sellerId;

        const updated = {
            bank_name: document.getElementById('bankNameInput').value,
            bank_account_number: document.getElementById('bankAccountInput').value,
            ifsc_code: document.getElementById('ifscCodeInput').value,
            upi_id: document.getElementById('upiIdInput').value
        };

        // Update visible fields instantly
        document.getElementById("bankNameText").textContent = updated.bank_name || '—';
        document.getElementById("bankAccountText").textContent = updated.bank_account_number || '—';
        document.getElementById("ifscCodeText").textContent = updated.ifsc_code || '—';
        document.getElementById("upiIdText").textContent = updated.upi_id || '—';

        try {
            const response = await fetch(`/admin/sellers/${sellerId}/bank-info`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(updated)
            });

            if (!response.ok) {
                const text = await response.text();
                throw new Error(`Server returned ${response.status}: ${text}`);
            }

            const result = await response.json();
            alert('✅ Bank information updated successfully!');
        } catch (error) {
            console.error('Error saving bank info:', error);
            alert('❌ Failed to save bank info. Check console for details.');
        }

        editDiv.classList.add('d-none');
        viewDiv.classList.remove('d-none');
    });

    // =====================================================
    // 🗑 DELETE SUPPORT TICKET
    // =====================================================
    document.querySelectorAll(".delete-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            if (!confirm("Are you sure you want to delete this ticket?")) return;

            fetch(`/admin/seller-supports/${btn.dataset.id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) btn.closest('tr').remove();
                    else alert("Failed to delete.");
                })
                .catch(() => alert("Failed to delete."));
        });
    });

    // =====================================================
    // 🧾 PRODUCT REVIEWS HANDLING
    // =====================================================
    let allReviews = [];

    function loadProductReviews(productId) {
        const list = document.getElementById("reviewList");
        list.innerHTML = `<p class="text-muted text-center">Loading reviews...</p>`;

        fetch(`/admin/products/${productId}/reviews`)
            .then(res => res.json())
            .then(data => {
                allReviews = data.reviews || [];
                const latestReview = allReviews.length ? [allReviews[0]] : [];
                renderReviews(latestReview);
            })
            .catch(() => {
                list.innerHTML = `<p class="text-muted text-center">Failed to load reviews.</p>`;
            });
    }

    function renderReviews(reviews) {
        const list = document.getElementById("reviewList");
        list.innerHTML = "";

        if (!reviews.length) {
            list.innerHTML = `<p class="text-muted text-center">No reviews available.</p>`;
            return;
        }

        reviews.forEach(r => {
            const stars = "⭐".repeat(r.rating || 0);
            const date = r.created_at ? new Date(r.created_at).toLocaleDateString() : "";
            const imageUrl = r.product_image
                ? `/storage/${r.product_image}`
                : '/images/no-image.png';

            list.innerHTML += `
                <div class="border rounded p-2 mb-3 shadow-sm">
                    <div class="d-flex align-items-center mb-2">
                        <img src="${imageUrl}" 
                             width="50" height="50" 
                             class="rounded me-3 border" 
                             onerror="this.src='/images/no-image.png'">
                        <div>
                            <strong>${r.customer_name || 'Anonymous'}</strong><br>
                            <small class="text-muted">${r.customer_email || 'No email provided'}</small>
                        </div>
                        <div class="ms-auto text-end">
                            <span class="small text-muted">${date}</span><br>
                            <span class="text-warning small">${stars}</span>
                        </div>
                    </div>
                    <p class="mb-2 small text-muted">${r.comment || ''}</p>
                </div>`;
        });
    }

    const searchInput = document.getElementById("reviewSearch");
    searchInput?.addEventListener("input", (e) => {
        const query = e.target.value.toLowerCase().trim();
        if (query === "") {
            const latestReview = allReviews.length ? [allReviews[0]] : [];
            renderReviews(latestReview);
            return;
        }
        const filtered = allReviews.filter(r =>
            (r.customer_name && r.customer_name.toLowerCase().includes(query)) ||
            (r.comment && r.comment.toLowerCase().includes(query))
        );
        renderReviews(filtered);
    });

    window.loadProductReviews = loadProductReviews;

    // =====================================================
    // ✏️ EDIT / DELETE REVIEW ACTIONS
    // =====================================================
    document.addEventListener("click", (e) => {
        if (e.target.closest(".edit-review")) {
            const card = e.target.closest(".border");
            const text = card.querySelector("p");
            const oldText = text.textContent.trim();

            text.outerHTML = `
                <textarea class="form-control form-control-sm mb-2">${oldText}</textarea>
                <div class="text-end">
                    <button class="btn btn-sm btn-success save-review" data-id="${e.target.dataset.id}">Save</button>
                    <button class="btn btn-sm btn-secondary cancel-edit">Cancel</button>
                </div>`;
        }

        if (e.target.closest(".cancel-edit")) {
            const productId = document.getElementById("productName").dataset.id;
            loadProductReviews(productId);
        }

        if (e.target.closest(".save-review")) {
            const card = e.target.closest(".border");
            const newText = card.querySelector("textarea").value;
            const id = e.target.dataset.id;

            fetch(`/admin/reviews/${id}`, {
                method: "PUT",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ review: newText }),
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) loadProductReviews(document.getElementById("productName").dataset.id);
                    else alert("Failed to update review");
                });
        }

        if (e.target.closest(".delete-review")) {
            const id = e.target.dataset.id;
            if (!confirm("Delete this review?")) return;

            fetch(`/admin/reviews/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) e.target.closest(".border").remove();
                    else alert("Failed to delete review");
                });
        }
    });
});




document.addEventListener("DOMContentLoaded", () => {
    const messageList = document.getElementById("messageList");
    const messageForm = document.getElementById("messageForm");
    const ticketInput = document.getElementById("ticketId");
    const trixEditor = document.querySelector("trix-editor");
    const attachmentPreviewArea = document.getElementById("attachmentPreviewArea");

    /**
     * 🟦 Load Messages
     */
    async function loadMessages(ticketId) {
        if (!ticketId) {
            messageList.innerHTML = `<p class="text-muted text-center">No ticket selected.</p>`;
            return;
        }

        messageList.innerHTML = `<p class="text-muted text-center">Loading...</p>`;

        try {
            const res = await fetch(`/admin/seller-supports/${ticketId}/messages`);
            if (!res.ok) throw new Error("Failed to fetch messages");
            const messages = await res.json();

            if (!messages.length) {
                messageList.innerHTML = `<p class="text-muted text-center">No messages yet.</p>`;
                return;
            }

            // Render messages
            messageList.innerHTML = messages.map(msg => {
                const isAdmin = msg.sender_type === "admin";
                const name = isAdmin
                    ? (msg.sender?.name || "Admin")
                    : (msg.sender?.shop_name || msg.sender?.name || "Seller");

                const align = isAdmin ? "text-end" : "text-start";
                const bubble = isAdmin ? "bg-primary text-white" : "bg-light border";

                let attachmentsHTML = "";

                // ✅ Handle multiple attachments (array)
                if (msg.attachment && Array.isArray(msg.attachment) && msg.attachment.length) {
                    attachmentsHTML = msg.attachment.map(fileUrl => {
                        const fileExtension = fileUrl.split('.').pop().toLowerCase();
                        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                            return `
                                <div class="mt-2">
                                    <img src="${fileUrl}" 
                                        alt="attachment"
                                        class="img-fluid rounded shadow-sm preview-trigger"
                                        data-file="${fileUrl}"
                                        data-type="image"
                                        style="max-width: 200px; max-height: 150px; object-fit: cover; cursor: pointer;">
                                </div>`;
                        } else {
                            return `
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-light preview-trigger"
                                            data-file="${fileUrl}"
                                            data-type="document">
                                        <i class="bi bi-paperclip"></i> View File
                                    </button>
                                </div>`;
                        }
                    }).join("");
                }

                return `
                    <div class="mb-3 ${align}">
                        <div class="d-inline-block p-3 rounded-4 shadow-sm ${bubble}">
                            <div class="fw-bold small">${name}</div>
                            <div>${msg.message || ""}</div>
                            ${attachmentsHTML}
                            <div class="text-muted small mt-1">${new Date(msg.created_at).toLocaleString()}</div>
                        </div>
                    </div>`;
            }).join("");

            messageList.scrollTo({ top: messageList.scrollHeight, behavior: "smooth" });

        } catch (err) {
            console.error("Load messages error:", err);
            messageList.innerHTML = `<p class="text-danger text-center">⚠️ Failed to load messages.</p>`;
        }
    }

    /**
     * 📎 Handle file attachments (prevent Trix default embed)
     */
    document.addEventListener("trix-file-accept", function (event) {
        event.preventDefault();

        const file = event.file;
        const fileURL = URL.createObjectURL(file);
        const fileExtension = file.name.split('.').pop().toLowerCase();
        let previewHTML = "";

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
            previewHTML = `
                <div class="position-relative">
                    <img src="${fileURL}" class="rounded shadow-sm" style="width:100px;height:80px;object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-attachment" title="Remove">
                        <i class="bi bi-x"></i>
                    </button>
                </div>`;
        } else {
            previewHTML = `
                <div class="border rounded px-3 py-2 bg-light d-flex align-items-center gap-2 position-relative">
                    <i class="bi bi-file-earmark-text"></i> ${file.name}
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-attachment" title="Remove">
                        <i class="bi bi-x"></i>
                    </button>
                </div>`;
        }

        const wrapper = document.createElement("div");
        wrapper.classList.add("attachment-wrapper", "position-relative");
        wrapper.dataset.fileName = file.name;
        wrapper.file = file; // store actual file
        wrapper.innerHTML = previewHTML;

        attachmentPreviewArea.appendChild(wrapper);
    });

    // 🗑️ Remove attachment preview
    attachmentPreviewArea.addEventListener("click", (e) => {
        if (e.target.closest(".remove-attachment")) {
            e.target.closest(".attachment-wrapper").remove();
        }
    });

    /**
     * ✉️ Submit message with attachments
     */
    messageForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const ticketId = ticketInput.value;
        if (!ticketId) return alert("⚠️ No ticket selected!");

        const messageHTML = trixEditor.editor.getDocument().toString().trim();
        const attachments = [...attachmentPreviewArea.querySelectorAll(".attachment-wrapper")];

        if (!messageHTML && attachments.length === 0) return;

        const sendBtn = messageForm.querySelector("button");
        sendBtn.disabled = true;
        sendBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Sending...`;

        const formData = new FormData();
        formData.append("message", messageHTML);

        // ✅ Append files correctly
        attachments.forEach((wrapper) => {
            if (wrapper.file) {
                formData.append("attachment[]", wrapper.file);
            }
        });

        try {
            const res = await fetch(`/admin/seller-supports/${ticketId}/messages`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });

            const data = await res.json();
            if (data.success) {
                attachmentPreviewArea.innerHTML = "";
                trixEditor.editor.loadHTML("");
                await loadMessages(ticketId);
            } else {
                console.error("Failed to send message:", data);
            }
        } catch (err) {
            console.error("Send message error:", err);
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = `<i class="bi bi-send"></i> Send`;
        }
    });

    /**
 * 👁️ Open ticket
 */
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".view-btn");
        if (!btn) return;

        const support = JSON.parse(btn.dataset.support);
        ticketInput.value = support.id;

        // 🏷️ Update Ticket ID
        const ticketIdSpan = document.getElementById("modalTicketId");
        if (ticketIdSpan) {
            ticketIdSpan.textContent = `#${support.id}`;
        }

        // 🏷️ Update Ticket Status (with color)
        const ticketStatusSpan = document.getElementById("modalTicketStatus");
        if (ticketStatusSpan) {
            const colors = {
                open: 'success',
                pending: 'warning',
                closed: 'secondary',
                reopened: 'primary'
            };
            const statusColor = colors[support.status] || 'secondary';
            ticketStatusSpan.className = `badge bg-${statusColor} ms-2`;
            ticketStatusSpan.textContent = support.status
                ? support.status.charAt(0).toUpperCase() + support.status.slice(1)
                : '—';
        }

        // 🧾 Load messages for this ticket
        loadMessages(support.id);

        // 🪟 Open modal
        const modalEl = document.getElementById("viewSupportModal");
        new bootstrap.Modal(modalEl).show();

        // =====================================================
        // 🔁 AUTO RELOAD AFTER MODAL CLOSE
        // =====================================================
        if (modalEl) {
            modalEl.addEventListener("hidden.bs.modal", () => {
                location.reload(); // reload the full page when modal closes
            });
        }

    });

    /**
     * 🖼️ Attachment preview modal
     */
    document.addEventListener("click", (e) => {
        const previewEl = e.target.closest(".preview-trigger");
        if (!previewEl) return;

        const fileUrl = previewEl.dataset.file;
        const fileType = previewEl.dataset.type;
        const modal = new bootstrap.Modal(document.getElementById("attachmentPreviewModal"));
        const previewContent = document.getElementById("previewContent");

        previewContent.innerHTML = `<p class="text-muted">Loading preview...</p>`;

        if (fileType === "image") {
            previewContent.innerHTML = `<img src="${fileUrl}" alt="Preview" class="img-fluid rounded shadow-sm">`;
        } else {
            previewContent.innerHTML = `
                <iframe src="${fileUrl}" 
                        class="w-100 border rounded shadow-sm" 
                        style="height: 80vh;"></iframe>`;
        }

        modal.show();
    });
});


function updateTicketStatus(status) {
    const map = {
        open: { color: 'success', icon: 'bi-unlock' },
        pending: { color: 'warning', icon: 'bi-hourglass-split' },
        processing: { color: 'info', icon: 'bi-gear' },
        close: { color: 'secondary', icon: 'bi-lock' },
    };
    const { color, icon } = map[status] || { color: 'secondary', icon: 'bi-question-circle' };
    const btn = document.getElementById('ticketStatusBtn');
    if (btn) {
        btn.className = `btn btn-sm btn-${color} rounded-pill shadow-sm px-3`;
        btn.innerHTML = `<i class="bi ${icon} me-1"></i><span class="fw-semibold text-capitalize">${status}</span>`;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const buttons = document.querySelectorAll('.status-btn');
    const ticketInput = document.getElementById('ticketId');

    buttons.forEach(button => {
        button.addEventListener('click', async function () {
            const status = this.getAttribute('data-status');
            const ticketId = ticketInput?.value;

            if (!ticketId) {
                alert('⚠️ Please open a ticket first.');
                return;
            }

            console.log('🎯 Updating ticket', ticketId, 'to', status);

            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';

            try {
                const response = await fetch(`/admin/seller-supports/${ticketId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                if (!response.ok) throw new Error(`Request failed (${response.status})`);

                const data = await response.json();

                if (!data.success) throw new Error(data.message || 'Failed to update status');

                console.log('✅ Updated:', data);

                // Visual feedback
                buttons.forEach(b => b.classList.remove('active', 'btn-primary'));
                this.classList.add('active', 'btn-primary');
                alert(`✅ Ticket #${ticketId} updated to "${data.status}".`);

            } catch (error) {
                console.error('❌ Error:', error);
                alert(error.message);
            } finally {
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    });
});

