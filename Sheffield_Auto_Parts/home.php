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
            <!-- View Order -->
            <div class="category-btn">
                <a href="view_orders.php">
                    <p>View Orders</p>
                </a>
            </div>
            <!-- View Users -->
            <div class="category-btn">
                <a href="view_users.php">
                    <p>View Users</p>
                </a>
            </div>
            <!-- View System Logs -->
            <div class="category-btn">
                <a href="systemlog.php">
                    <p>View System Logs</p>
                </a>
            </div>
            <!-- View Company Branches -->
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
            <!-- View Company Suppliers -->
            <div class="category-btn">
                <a href="view_suppliers.php">
                    <p>View Company Suppliers</p>
                </a>
            </div>
            <!-- View Stock Alerts -->
            <div class="category-btn">
                <a href="stock_alerts.php">
                    <p>View Stock Alerts</p>
                </a>
            </div>
            <!-- View Inventory -->
            <div class="category-btn">
                <a href="inventory.php">
                    <p>View Inventory</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>