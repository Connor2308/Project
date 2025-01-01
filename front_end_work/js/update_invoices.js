// Updating stock/quantity dynamically (no page refresh)
$(document).ready(function () {
    // Handle update and decrease of stock
    $('.update-btn').click(function () {
        const invoiceId = $(this).data('id');  // Get the invoice ID
        const action = $(this).data('action');  // Determine if action is to increase or decrease

        $.ajax({
            url: 'update_stock.php',  // Path to the PHP file that handles stock updates
            type: 'POST',
            data: {
                invoice_id: invoiceId,  // Send invoice_id to backend
                action: action,          // Send action (increase/decrease)
            },
            success: function (response) {
                // If the response is successful, update the total_due dynamically
                $(`#row-${invoiceId} .quantity`).text(response);  // Update the stock value dynamically in the table
            },
            error: function () {
                // If error occurs, show an alert
                alert('Error updating stock');
            }
        });
    });
});