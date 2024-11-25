<?php
include('include/init.php'); //initalise everything like user data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/tablelist.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
</head>
<body>

        
    <?php include('include/header.php')?>
    <div class="page-container">   
        <h2 class = "page-title">Orders</h2>

        <div class="table-container">
            <table class="inventory-list">
            <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>24/10/24</td>
                        <td>#123</td>
                        <td>Shipped</td>
                        <td>2</td>
                    </tr>
                </tbody>
</table>
        </div>


    </div>
    <?php include('include/footer.php')?>
</body>
</html>