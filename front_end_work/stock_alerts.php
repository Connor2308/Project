<?php
include('include/init.php'); // Initialise, includes the database connection

// Fetch parts with stock levels below the reorder level
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
        WHERE parts.quantity_in_stock < parts.reorder_level";
$result = $con->query($sql);

if (!$result) {
    die("Error fetching stock alerts: " . $con->error);
}
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
        
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th>Part ID</th>
                        <th>Part Name</th>
                        <th>Quantity in Stock</th>
                        <th>Reorder Level</th>
                        <th>Supplier</th>
                        <th>Branch</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['part_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['part_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity_in_stock']); ?></td>
                                <td><?php echo htmlspecialchars($row['reorder_level']); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No stock alerts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include('include/footer.php'); ?>
</body>
</html>