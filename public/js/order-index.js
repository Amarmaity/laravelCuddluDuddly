$(function () {
    let selectedOrderId = null;

    // Open modal
    $(document).on('click', '.cancel-order-btn', function () {
        selectedOrderId = $(this).data('order-id');
        $('#cancelOrderModal').modal('show');
    });

    // Toggle textarea for "Other"
    $(document).on('change', 'input[name="reason"]', function () {
        if ($('#reasonOther').is(':checked')) {
            $('#reasonText').removeClass('d-none');
        } else {
            $('#reasonText').addClass('d-none').val('');
        }
    });


    const storeUrl = $('#cancelOrderModal').data('store-url');

    // Handle form submission
    $('#cancelOrderForm').on('submit', function (e) {
        e.preventDefault();

        let reason = $('input[name="reason"]:checked').val();
        if (reason === 'other') reason = $('#reasonText').val();

        if (!reason) {
            alert('Please select or enter a reason.');
            return;
        }

        $.ajax({
            url: storeUrl,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                order_id: selectedOrderId,
                reason: reason
            },
            beforeSend: function () {
                $('#cancelOrderForm button[type="submit"]').prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm"></span> Processing...'
                    );
            },
            success: function (res) {
                $('#cancelOrderModal').modal('hide');
                $('#cancelOrderForm')[0].reset();
                $('#reasonText').addClass('d-none');
                alert(res.message || 'Order cancellation request is submitted.');
                window.location.reload();
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Failed to cancel order.');
            },
            complete: function () {
                $('#cancelOrderForm button[type="submit"]').prop('disabled', false)
                    .html('<i class="bi bi-check-circle"></i> Confirm Cancellation');
            }
        });
    });
});

$(document).on('click', '.order-row', function () {
    const orderId = $(this).data('id');
    const modal = $('#orderQuickViewModal');
    const content = $('#orderQuickViewContent');

    modal.modal('show');
    content.html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 mb-0">Loading order details...</p>
                    </div>`);

    $.ajax({
        url: `orders/${orderId}/quick-view`,
        type: 'GET',
        success: function (res) {
            if (res.success) {
                content.html(res.html);
            } else {
                content.html(`<div class="p-4 text-danger">${res.message}</div>`);
            }
        },
        error: function () {
            content.html(`<div class="p-4 text-danger">Failed to fetch order details.</div>`);
        }
    });
});