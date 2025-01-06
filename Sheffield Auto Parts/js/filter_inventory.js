//dynamic filtering so we dont need to refresh the page each time
$(document).ready(function () {
    $('#apply-filters').on('click', function () {
        const genre = $('#genre').val();
        const minPrice = $('#min_price').val();
        const maxPrice = $('#max_price').val();

        $.ajax({
            url: 'inventory.php',
            method: 'POST',
            data: { genre, min_price: minPrice, max_price: maxPrice },
            success: function (response) {
                const tableBody = $(response).find('#inventory-table').html();
                $('#inventory-table').html(tableBody);
            },
            error: function () {
                alert('An error occurred while applying filters.');
            }
        });
    });
});
