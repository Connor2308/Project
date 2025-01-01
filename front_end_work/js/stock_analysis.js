$(document).ready(function() {
    // Function to fetch stock data and update the table and chart
    function fetchStockData() {
        var expectedStock = 75; // Minimum stock threshold
        $.ajax({
            url: 'connection.php', // PHP file that fetches data
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var partNames = response.part_names; // Get part names from response
                var stockQuantities = response.quantity_in_stock; // Get stock quantities from response

                // Insert data into the table
                insertStockData(partNames, stockQuantities);

                // Call function to update the chart
                updateChart(partNames, stockQuantities, expectedStock);
            }
        });
    }

    // Function to insert data into the table
    function insertStockData(partNames, stockQuantities) {
        var tableBody = $('#stockTable tbody'); // Get the table body element
        tableBody.empty(); // Clear any existing data in the table
        
        // Loop through the part names and stock quantities
        partNames.forEach(function(part, index) {
            var row = '<tr>' +
                          '<td>' + part + '</td>' +
                          '<td>' + stockQuantities[index] + '</td>' +
                      '</tr>';
            tableBody.append(row); // Add the row to the table
        });
    }

    // Function to update the chart with new data
    function updateChart(partNames, stockQuantities, expectedStock) {
        var ctx = document.getElementById('StockData').getContext('2d');

        // Set up the colors: default is green, highlight is red if stock is below expected
        var pointColors = stockQuantities.map(function(stock) {
            return stock < expectedStock ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)';
        });

        // Create the chart
        var stockChart = new Chart(ctx, {
            type: 'bar', // Bar chart for better visualization of stock levels
            data: {
                labels: partNames, // X-axis labels (part names)
                datasets: [{
                    label: 'Stock Levels',
                    data: stockQuantities, // Y-axis data (stock quantities)
                    backgroundColor: pointColors, // Set background color to highlight low stock
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Initial call to fetch and display data
    fetchStockData();
});
