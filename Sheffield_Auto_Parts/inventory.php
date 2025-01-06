<?php
include('include/init.php'); // Initialise, includes the database connection

// Sorting Section
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'part_id'; // Default sort column
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC'; // Default sort order
$next_sort_order = ($sort_order == 'ASC') ? 'DESC' : 'ASC'; // Toggle sort order

// Handling filters
$selected_genres = isset($_POST['genres']) ? $_POST['genres'] : [];
$min_price = isset($_POST['min_price']) && is_numeric($_POST['min_price']) ? $_POST['min_price'] : null;
$max_price = isset($_POST['max_price']) && is_numeric($_POST['max_price']) ? $_POST['max_price'] : null;
$branch_filter = isset($_POST['branch']) ? $_POST['branch'] : '';

// Fetch all genres for the dropdown
$genre_sql = "SELECT DISTINCT genre FROM parts"; // where i learnt about distinct is here https://www.w3schools.com/sql/sql_distinct.asp
$genre_result = $con->query($genre_sql);
$genres = [];
while ($row = $genre_result->fetch_assoc()) {
    $genres[] = $row['genre'];
}

// Fetch branches for the dropdown
$branches_sql = "SELECT branch_id, branch_name, active FROM branches";
$branches_result = $con->query($branches_sql);
$branches = [];
while ($row = $branches_result->fetch_assoc()) {
    $branches[] = $row;
}

// Build the query based on the filters
$sql = "SELECT 
            parts.part_id, 
            parts.part_name, 
            parts.description, 
            parts.genre, 
            parts.manufacturer, 
            parts.unit_price, 
            parts.quantity_in_stock, 
            parts.reorder_level, 
            suppliers.supplier_name,
            suppliers.active AS supplier_active,
            branches.branch_name,
            branches.active AS branch_active
        FROM parts 
        INNER JOIN suppliers ON parts.supplier_id = suppliers.supplier_id
        LEFT JOIN branches ON parts.branch_id = branches.branch_id";

$conditions = []; // Array to store conditions

if ($min_price !== null) {
    $conditions[] = "parts.unit_price >= $min_price";
}
if ($max_price !== null) {
    $conditions[] = "parts.unit_price <= $max_price";
}
if (!empty($selected_genres) && !in_array("", $selected_genres)) {
    $genre_list = implode("','", array_map('addslashes', $selected_genres));
    $conditions[] = "parts.genre IN ('$genre_list')";
}
if (!empty($branch_filter)) {
    $conditions[] = "parts.branch_id = '$branch_filter'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY $sort_column $sort_order";

$result = $con->query($sql);

// Delete part
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_part'])) {
    $part_id = $_POST['part_id'];

    // Begin transaction to ensure data consistency
    $con->begin_transaction();

    try {
        // Delete all items in the order
        $delete_items_sql = "DELETE FROM order_items WHERE part_id = ?";
        $delete_items_stmt = $con->prepare($delete_items_sql);
        $delete_items_stmt->bind_param('i', $part_id);
        if (!$delete_items_stmt->execute()) {
            throw new Exception('Error deleting item from order items.');
        }

        // Delete the order itself
        $delete_part_sql = "DELETE FROM parts WHERE part_id = ?";
        $delete_part_stmt = $con->prepare($delete_part_sql);
        $delete_part_stmt->bind_param('i', $part_id);
        if (!$delete_part_stmt->execute()) {
            throw new Exception('Error deleting the parts.');
        }

        // Commit the transaction
        $con->commit();
        logAction($user_data['user_id'], $user_data['username'], 'DELETE', "Deleted Part ID: $part_id"); // Log the action here
        header('Location: inventory.php'); // Redirect to parts page after successful removal
        exit;
    } catch (Exception $e) {
        // Rollback in case of error
        $con->rollback();
        echo "<p>Error removing part: " . $e->getMessage() . "</p>";
    }
}
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <title>Car Parts Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php include('include/header.php'); ?>

    <div class="page-container">
        <h2 class="page-title">Car Parts Inventory</h2>

        <!-- Export Inventory as PDF Button -->
        <div class="button-container">
            <a href="generate_inventory_report.php" class="export-btn">Export Inventory as PDF</a>
        </div>

        <!-- Filter Section -->
        <form action="inventory.php" method="POST" class="adding-form">
            <h3>Filter Parts</h3>
            <div class="form-columns">
                <!-- Genres -->
                <div class="left-column">
                    <div class="form-box">
                        <label for="genres">Genres:</label>
                        <select id="genres" name="genres[]" multiple>
                            <option value="">All Genres</option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo htmlspecialchars($genre); ?>" <?php echo in_array($genre, $selected_genres) ? 'selected' : ''; ?>><?php echo htmlspecialchars($genre); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="right-column">
                    <!-- Branch -->
                    <div class="form-box">
                        <label for="branch">Branch:</label>
                        <select id="branch" name="branch">
                            <option value="">All Branches</option>
                            <?php foreach ($branches as $branch): ?>
                                <?php $branch_status = $branch['active'] ? '' : ' (Closed)'; ?>
                                <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>" <?php echo ($branch_filter == $branch['branch_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($branch['branch_name']) . $branch_status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- min price -->
                    <div class="form-box">
                        <label for="min_price">Min Price:</label>
                        <input type="number" step="0.01" id="min_price" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>">
                    </div>
                    <!--Max price -->
                    <div class="form-box">
                        <label for="max_price">Max Price:</label>
                        <input type="number" step="0.01" id="max_price" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>">
                    </div>
                </div>
            </div>
            <button type="submit" class="save-btn">Apply Filters</button>
        </form>

        <!-- Add a Part Button -->
        <div id="add-part-button-container" class="button-container">
            <a href="adding_new_parts.php" class="add-part-btn">Add Part</a> 
        </div>

        <!-- Inventory Table -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th><a href="?sort_column=part_id&sort_order=<?php echo $next_sort_order; ?>">Part ID</a></th>
                        <th><a href="?sort_column=part_name&sort_order=<?php echo $next_sort_order; ?>">Part Name</a></th>
                        <th><a href="?sort_column=description&sort_order=<?php echo $next_sort_order; ?>">Description</a></th>
                        <th><a href="?sort_column=genre&sort_order=<?php echo $next_sort_order; ?>">Type</a></th>
                        <th><a href="?sort_column=manufacturer&sort_order=<?php echo $next_sort_order; ?>">Manufacturer</a></th>
                        <th><a href="?sort_column=unit_price&sort_order=<?php echo $next_sort_order; ?>">Unit Price</a></th>
                        <th><a href="?sort_column=supplier_name&sort_order=<?php echo $next_sort_order; ?>">Supplier</a></th>
                        <th><a href="?sort_column=branch_name&sort_order=<?php echo $next_sort_order; ?>">Branch</a></th>
                        <th><a href="?sort_column=reorder_level&sort_order=<?php echo $next_sort_order; ?>">Reorder Level</a></th>
                        <th><a href="?sort_column=quantity_in_stock&sort_order=<?php echo $next_sort_order; ?>">Quantity in Stock</a></th>
                        <th>Update Stock Levels</th>
                        <th>Manage Part Details</th>
                        <th>Remove Part</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $branch_status = $row['branch_active'] ? '' : ' (Closed)';
                            $supplier_status = $row['supplier_active'] ? '' : ' (Old)';
                            echo "<tr id='row-{$row['part_id']}'>";
                            echo "<td>" . htmlspecialchars($row['part_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['part_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['genre']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['manufacturer']) . "</td>";
                            echo "<td>£" . number_format($row['unit_price'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['supplier_name']) . $supplier_status . "</td>";
                            echo "<td>" . htmlspecialchars($row['branch_name']) . $branch_status . "</td>";
                            echo "<td>" . htmlspecialchars($row['reorder_level']) . "</td>";
                            echo "<td class='quantity'>" . htmlspecialchars($row['quantity_in_stock']) . "</td>";
                            echo "<td>
                                    <div class='button-container'>
                                        <button class='update-btn' data-id='{$row['part_id']}' data-action='decrease'>-</button>
                                        <button class='update-btn' data-id='{$row['part_id']}' data-action='increase'>+</button>
                                    </div>
                                  </td>";
                            echo "<td><a href='manage_part.php?part_id=" . htmlspecialchars($row['part_id']) . "' class='manage-btn'>Manage Part</a></td>";
                            echo "<td>
                                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this part?\");'>
                                        <input type='hidden' name='part_id' value='" . htmlspecialchars($row['part_id']) . "'>
                                        <button type='submit' name='remove_part' class='remove-btn'>Remove Part</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13'>No parts found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Include JavaScript files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/update_stock.js"></script>
</body>
</html>