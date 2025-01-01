<?php
include('include/init.php'); // initialize everything like user data

// Sorting Section
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'invoice_id'; // Default sort column
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC'; // Default sort order
$next_sort_order = ($sort_order == 'ASC') ? 'DESC' : 'ASC'; // Toggle between ASC and DESC

// Handling filters
$genre_filter = isset($_POST['genre']) ? $_POST['genre'] : [];
$min_price = isset($_POST['min_price']) && is_numeric($_POST['min_price']) ? $_POST['min_price'] : null;
$max_price = isset($_POST['max_price']) && is_numeric($_POST['max_price']) ? $_POST['max_price'] : null;

// Allowed sorting columns to prevent SQL injection
$allowed_sort_columns = ['invoice_id', 'order_name', 'invoice_date', 'invoice_time', 'total_due'];
if (!in_array($sort_column, $allowed_sort_columns)) {
    $sort_column = 'invoice_id'; // Default to a safe column
}

// SQL selection to populate the table, including a join for the suppliers ID
$sql = "SELECT 
            invoices.invoice_id, 
            orders.order_name,
            invoices.invoice_date, 
            invoices.invoice_time, 
            invoices.total_due
        FROM invoices 
        INNER JOIN order ON invoices.order_id = orders.order_id";

// Adding filters to the query
$conditions = []; // Store conditions like filters
if ($min_price !== null) {
    $conditions[] = "invoices.total_due >= $min_price";
}
if ($max_price !== null) {
    $conditions[] = "invoices.total_due <= $max_price";
}
if (!empty($genre_filter)) {
    $genre_list = implode("','", array_map('addslashes', $genre_filter));
    $conditions[] = "invoices.genre IN ('$genre_list')"; // Only get specific genres of invoices
}

// If conditions exist, add them to the SQL
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Adding the ORDER BY clause
$sql .= " ORDER BY $sort_column $sort_order";

// Execute the query
$result = $con->query($sql);

// Handling the removal of an invoice
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_invoice'])) {
    $invoice_id = $_POST['invoice_id'];

    // Begin transaction for data consistency
    $con->begin_transaction();

    try {
        // Delete the invoice
        $delete_invoice_sql = "DELETE FROM invoices WHERE invoice_id = ?";
        $delete_invoice_stmt = $con->prepare($delete_invoice_sql);
        $delete_invoice_stmt->bind_param('i', $invoice_id);
        if (!$delete_invoice_stmt->execute()) {
            throw new Exception('Error deleting the invoice.');
        }

        // Commit the transaction
        $con->commit();
        logAction($user_data['user_id'], $user_data['username'], 'DELETE', "Deleted Invoice ID: $invoice_id"); // Log the action
        header('Location: inventory.php'); // Redirect after successful removal
        exit;
    } catch (Exception $e) {
        // Rollback in case of error
        $con->rollback();
        echo "<p>Error removing invoice: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parts Inventory</title>
</head>
<body>
    <?php include('include/header.php'); ?>

    <div class="page-container">
        <h2 class="page-title">Car Parts Inventory Invoices</h2>

        <!-- Filter Form -->
        <form id="filter-form" method="POST">
            <div class="filters-left">
                <label for="min_price">Min Price:</label>
                <input type="number" id="min_price" name="min_price" min="0" step="0.01" value="<?php echo isset($_POST['min_price']) ? htmlspecialchars($_POST['min_price']) : ''; ?>">

                <label for="max_price">Max Price:</label>
                <input type="number" id="max_price" name="max_price" min="0" step="0.01" value="<?php echo isset($_POST['max_price']) ? htmlspecialchars($_POST['max_price']) : ''; ?>">
            </div>

            <div class="filters-right">
                <label>Filter by invoices:</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="engine" name="genre[]" value="Engine" <?php echo in_array('Engine', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="engine">Invoice date</label>

                    <input type="checkbox" id="exhaust" name="genre[]" value="Exhaust" <?php echo in_array('Exhaust', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="exhaust">Time</label>

                    <input type="checkbox" id="body" name="genre[]" value="Body" <?php echo in_array('Body', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="body">Body</label>

                    <input type="checkbox" id="brakes" name="genre[]" value="Brakes" <?php echo in_array('Brakes', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="brakes">Brakes</label>

                    <input type="checkbox" id="transmission" name="genre[]" value="Transmission" <?php echo in_array('Transmission', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="transmission">Transmission</label>
                </div>
            </div>

            <div class="filters-search">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" placeholder="Search invoices..." value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
            </div>

            <button type="submit" id="apply-filters" disabled>Apply Filters</button> 
        </form>

        <div id="add-part-button-container" class="button-container">
            <a href="adding_new_parts.php" class="add-part-btn">Add Invoice</a>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th><a href="?sort_column=invoice_id&sort_order=<?php echo $next_sort_order; ?>">Invoice Id</a></th>
                        <th><a href="?sort_column=order_name&sort_order=<?php echo $next_sort_order; ?>">Order Name</a></th>
                        <th><a href="?sort_column=invoice_date&sort_order=<?php echo $next_sort_order; ?>">Invoice Date</a></th>
                        <th><a href="?sort_column=invoice_time&sort_order=<?php echo $next_sort_order; ?>">Invoice Time</a></th>
                        <th><a href="?sort_column=total_due&sort_order=<?php echo $next_sort_order; ?>">Total Due</a></th>
                        <th>Remove Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['invoice_id']}'>";
                            echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['order_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['invoice_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['invoice_time']) . "</td>";
                            echo "<td>£" . number_format($row['total_due'], 2) . "</td>";
                            echo "<td>
                                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this invoice?\");'>
                                        <input type='hidden' name='invoice_id' value='" . htmlspecialchars($row['invoice_id']) . "'>
                                        <button type='submit' name='remove_invoice' class='remove-btn'>Remove Invoice</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No Invoices found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('include/footer.php'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/filter_inventory.js"></script>
    <script src="js/filter_validation.js"></script>
    <script src="js/update_invoices.js"></script>
</body>
</html>