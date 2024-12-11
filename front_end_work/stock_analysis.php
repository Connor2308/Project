<?php
include('include/init.php');
include('include/chart_functionality.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/category.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include('include/header.php')?>
    <div class="page-container">   

    <h1>Stock Data</h1>

    <canvas id="StockData" width="400" height="200"></canvas>

<script>
$(document).ready(function() {
    // Function to fetch data and update the chart
    function fetchSalesData() {
        var exceptedstock = 75;//this is to set the minimum stock theshold that is accepted and any thing under that will show up red.
        $.ajax({
            url: 'connection.php', // PHP file that fetches data
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var products = response.products;
                var stocks = response.stocks;
                updateChart(products, stocks, expectedstock); // Call function to update the chart
            }
        });
    }

    // Function to update the chart with new data
    function updateChart(products, stocks, expectedstock) {
        var ctx = document.getElementById('StockData').getContext('2d');

        // Find the indexes where sales are lower than expected
        var highlightIndices = sales.map(function(value, index) {
            return value < expectedSales ? index : -1;
        }).filter(function(value) { return value !== -1; });

        // Set up the colors: default is green, highlight is red
        var pointColors = sales.map(function(value) {
            return value < expectedSales ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)';
        });
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: products, // X-axis labels (products)
                datasets: [{
                    label: 'Sales Over Time',
                    data: stocks, // Y-axis data (stocks)
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Initial call to fetch and display data
    fetchSalesData();
});
</script>
    </div>
    <?php include('include/footer.php')?>
</body>
</html>