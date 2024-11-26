<?php
include('include/init.php'); //initalise everything like user data
?>
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

    <a href="inventory.php">
        <div class="category-btn">
        <p>Check Stock</p>
        </div>

        <a href="stock.php">
        <div class="category-btn">
        <p>Update Stock</p>
        </div></a>
        
        <a href="suppliers.php"><div class="category-btn">
        <p>Suppliers</p>
        </div></a>

        <a href="suppliers.php">
        <div class="category-btn">
        <p>Manage Suppliers</p>
        </div></a>

        <a href="orders.php">
        <div class="category-btn">
        <p>Manage Orders</p>
        </div></a>

        <a href="stafflist.php">
        <div class="category-btn">
        <p>View Staff Accounts</p>
        </div></a>

    </div>

    </div>
    <?php include('include/footer.php')?>
</body>
</html>