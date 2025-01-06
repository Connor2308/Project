<?php
include('include/init.php');

$records_per_page = 10; // Number of records to display per page

$sql_count = "SELECT COUNT(*) AS total FROM parts WHERE quantity_in_stock < reorder_level"; // Count total records whre quantity_in_stock is less than reorder_level
$result_count = $con->query($sql_count); // Execute the query
$total_records = $result_count->fetch_assoc()['total']; // Get the total records

$total_pages = ceil($total_records / $records_per_page); // Calculate total pages

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page number
$start_from = ($page - 1) * $records_per_page; // Calculate the starting record for the query

// Ordering section
$order_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'part_id';
$order_dir = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$order_dir = ($order_dir === 'ASC') ? 'DESC' : 'ASC';

$search_term = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%"; //when the search term is empty, it will return all the results

// Correcting the SQL query
$sql = "SELECT 
            parts.part_id, 
            parts.part_name, 
            parts.quantity_in_stock, 
            parts.reorder_level, 
            suppliers.supplier_name,
            branches.branch_name
        FROM parts 
        INNER JOIN suppliers ON parts.supplier_id = suppliers.supplier_id
        LEFT JOIN branches ON parts.branch_id = branches.branch_id
        WHERE parts.quantity_in_stock < parts.reorder_level
        AND (parts.part_name LIKE ? OR suppliers.supplier_name LIKE ?)
        ORDER BY $order_by $order_dir
        LIMIT $start_from, $records_per_page";
        // Added the search term to the WHERE clause also added the ORDER BY clause.
        // Also added the LIMIT clause to limit the number of records per page

// Prepare and bind parameters
$stmt = $con->prepare($sql);
$stmt->bind_param('ss', $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Alerts</title>
</head>
<body>
    <?php include('include/header.php'); ?>

    <div class="page-container">
        <h2 class="page-title">Stock Alerts</h2>
        <!-- Search Bar -->
        <div class="search-bar">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Search by part name or supplier" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th><a href="?sort_by=part_id&order=<?php echo $order_dir; ?>">Part ID</a></th>
                        <th><a href="?sort_by=part_name&order=<?php echo $order_dir; ?>">Part Name</a></th>
                        <th><a href="?sort_by=quantity_in_stock&order=<?php echo $order_dir; ?>">Quantity in Stock</a></th>
                        <th><a href="?sort_by=reorder_level&order=<?php echo $order_dir; ?>">Reorder Level</a></th>
                        <th><a href="?sort_by=supplier_name&order=<?php echo $order_dir; ?>">Supplier</a></th>
                        <th><a href="?sort_by=branch_name&order=<?php echo $order_dir; ?>">Branch</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['part_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['part_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity_in_stock']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reorder_level']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['supplier_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['branch_name']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No stock alerts found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='stock_alerts.php?page=$i&sort_by=$order_by&order=$order_dir' class='" . ($i == $page ? 'active' : '') . "'>$i</a>";
            }
            ?>
        </div>
    </div>
</body>
</html>
