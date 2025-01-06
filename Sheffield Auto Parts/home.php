<?php
include('include/init.php'); //initalise everything like user data
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/category.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>
    
    <?php include('include/header.php')?>
    <div class="page-container">   
        <div class="categories">
            <div class="category-btn">
                <a href="view_orders.php">
                    <p>View Orders</p>
                </a>
            </div>
            <div class="category-btn">
                <a href="view_users.php">
                    <p>View Users</p>
                </a>
            </div>
            <div class="category-btn">
                <a href="systemlog.php">
                    <p>View System Logs</p>
                </a>
            </div>
            <div class="category-btn">
                <a href="view_branches.php">
                    <p>View Company Branches</p>
                </a>
            </div>
            <!-- <div class="category-btn">
                <a href="stock_analysis.php">
                    <p>Stock Analysis</p>
                </a>
            </div> -->
            <div class="category-btn">
                <a href="view_suppliers.php">
                    <p>View Company Suppliers</p>
                </a>
            </div>
            <div class="category-btn">
                <a href="stock_alerts.php">
                    <p>View Stock Alerts</p>
                </a>
            </div>
            <div class="category-btn">
                <a href="inventory.php">
                    <p>View Inventory</p>
                </a>
            </div>
        </div>
    </div>
    <?php include('include/footer.php')?>
</body>
</html>