$(document).ready(function() {
    $('#invoice-form').on('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way

        var totalPaid = parseFloat($('#total_paid').val());
        var totalCost = parseFloat($('#total_cost').text().replace('£', '').replace(',', ''));
        var orderId = $('#order_id').val();

        if (totalPaid < 0 || totalPaid > totalCost) {
            alert('Total paid must be between 0 and the total cost.');
            return;
        }

        $.ajax({
            url: 'update_invoice.php',
            type: 'POST',
            data: {
                order_id: orderId,
                total_paid: totalPaid
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert('Invoice updated successfully.');
                } else {
                    alert('Error updating invoice: ' + data.message);
                }
            },
            error: function() {
                alert('Error updating invoice.');
            }
        });
    });
});