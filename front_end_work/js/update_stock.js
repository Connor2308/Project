//updating quantity dynamically avoiding page refreshes (with JQuery and AJAX).
$(document).ready(function () { //loading the dom in first
    $('.update-btn').click(function () { //jquery click event handler to add a listener to see if someone wants to add or remove stock
        const partId = $(this).data('id');
        const action = $(this).data('action');
        $.ajax({
            url: 'update_stock.php',
            type: 'POST',
            data: { part_id: partId, action: action },
            success: function (response) {
                $(`#row-${partId} .quantity`).text(response);//if the response is success full then it will update the quantitys stock of the designated Part ID
            },
            error: function () { //any errors will show this.
                alert('Error updating stock');
            }
        });
    });
});
