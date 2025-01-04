$(document).ready(function() {
    $('.update-btn').click(function() {
        var partId = $(this).data('id');
        var action = $(this).data('action');

        $.ajax({
            url: 'update_stock.php',
            type: 'POST',
            data: {
                part_id: partId,
                action: action
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#row-' + partId + ' .quantity').text(data.quantity_in_stock);
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Error updating stock level.');
            }
        });
    });
});