<!-- HTML -->
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
    <?php include('include/header.php'); ?>
    
    <div class="page-container">
        <h1>Stock Data</h1>
        <table id="stockTable">
            <thead>
                <tr>
                    <th>Part Name</th>
                    <th>Quantity in Stock</th>
                </tr>
            </thead>
            <tbody>


            
                <!-- Data will be inserted here by JavaScript -->
            </tbody>
        </table>

        <canvas id="StockData" width="400" height="200"></canvas>
    </div>

    <?php include('include/footer.php'); ?> 
</body>
</html>

<?php
include('include/connection.php');

$query = "SELECT part_name, quantity_in_stock FROM parts";
$result = mysqli_query($conn, $query);
$data = [];
if ($result) {
    $data['part_names'] = [];
    $data['quantity_in_stock'] = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data['part_names'][] = $row['part_name'];
        $data['quantity_in_stock'][] = $row['quantity_in_stock'];
    }

    if (empty($data['part_names']) || empty($data['quantity_in_stock'])) {
        echo "<pre>No data found!</pre>"; 
    } else {
        echo "<pre>Data fetched successfully:</pre>";
        var_dump($data); 
    }

    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Failed to fetch data from the database']);
}
?>



<script>
$(document).ready(function() {
    function fetchStockData() {
        var expectedStock = 75; 

        $.ajax({
            url: 'connection.php', 
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert('Error fetching data: ' + response.error);
                    return;
                }

                var products = response.part_names; 
                var stocks = response.quantity_in_stock; 
                insertStockData(products, stocks);

                updateChart(products, stocks, expectedStock);
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error); 
            }
        });
    }

    function insertStockData(products, stocks) {
        var tableBody = $('#stockTable tbody'); 
        tableBody.empty(); 
        
        products.forEach(function(product, index) {
            var row = '<tr>' +
                          '<td>' + product + '</td>' +
                          '<td>' + stocks[index] + '</td>' +
                      '</tr>';
            tableBody.append(row); 
        });
    }
    function updateChart(products, stocks, expectedStock) {
        var ctx = document.getElementById('StockData').getContext('2d');

        var pointColors = stocks.map(function(stock) {
            return stock < expectedStock ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)';
        });

        var stockChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: products, 
                datasets: [{
                    label: 'Stock Levels',
                    data: stocks,
                    backgroundColor: pointColors, 
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

    fetchStockData();
});
</script>
    <?php include('include/footer.php')?>
