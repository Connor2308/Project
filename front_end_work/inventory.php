<?php
session_start();
include('include/functions.php');
include('include/connection.php');
$user_data = check_login($con);//to get the data of the currently logged in user
$user_id = $user_data['user_id'];

//handling the sorting part. using what is in the url to either sort to asc or desc if nothing then it will default to part id eg when the page is first loaded
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'part_id';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

$next_sort_order = ($sort_order == 'ASC') ? 'DESC' : 'ASC';

//selecting all bits needed to be displayed
$sql = "SELECT 
            parts.part_id, 
            parts.part_name, 
            parts.description, 
            parts.genre, 
            parts.manufacturer, 
            parts.unit_price, 
            parts.quantity_in_stock, 
            parts.reorder_level, 
            suppliers.supplier_name 
        FROM parts 
        INNER JOIN suppliers ON parts.supplier_id = suppliers.supplier_id
        ORDER BY $sort_column $sort_order";

$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parts Inventory</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
           <!-- page header -->
        <h2 class="page-title">Car Parts Inventory</h2>

        <div class="table-container">
            <table class="inventory-list">
                <!-- table headers -->
                <thead> 
                    <tr>
                        <th><a href="?sort_column=part_id&sort_order=<?php echo $next_sort_order; ?>">Part ID</a></th>
                        <th><a href="?sort_column=part_name&sort_order=<?php echo $next_sort_order; ?>">Part Name</a></th>
                        <th><a href="?sort_column=description&sort_order=<?php echo $next_sort_order; ?>">Description</a></th>
                        <th><a href="?sort_column=genre&sort_order=<?php echo $next_sort_order; ?>">Genre</a></th>
                        <th><a href="?sort_column=manufacturer&sort_order=<?php echo $next_sort_order; ?>">Manufacturer</a></th>
                        <th><a href="?sort_column=unit_price&sort_order=<?php echo $next_sort_order; ?>">Unit Price</a></th>
                        <th><a href="?sort_column=supplier_name&sort_order=<?php echo $next_sort_order; ?>">Supplier</a></th>
                        <th><a href="?sort_column=reorder_level&sort_order=<?php echo $next_sort_order; ?>">Reorder Level</a></th>
                        <th><a href="?sort_column=quantity_in_stock&sort_order=<?php echo $next_sort_order; ?>">Quantity in Stock</a></th>
                        <th>Update Stock Levels</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //this is for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['part_id']}'>";
                            echo "<td>" . htmlspecialchars($row['part_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['part_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['genre']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['manufacturer']) . "</td>";
                            echo "<td>£" . number_format($row['unit_price'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['supplier_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reorder_level']) . "</td>";
                            echo "<td class='quantity'>" . htmlspecialchars($row['quantity_in_stock']) . "</td>";
                            echo "<td>
                                    <button class='update-btn' data-id='{$row['part_id']}' data-action='decrease'>-</button>
                                    <button class='update-btn' data-id='{$row['part_id']}' data-action='increase'>+</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td>No parts found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/update_stock.js"></script>
</body>
</html>
