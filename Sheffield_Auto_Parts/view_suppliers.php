<?php
include('include/init.php');
// Handle deactivating a supplier
if (isset($_GET['deactivate_supplier_id'])) {
    $deactivate_supplier_id = intval($_GET['deactivate_supplier_id']);
    $deactivate_sql = "UPDATE suppliers SET active = 0 WHERE supplier_id = ?";
    $stmt = $con->prepare($deactivate_sql);
    $stmt->bind_param('i', $deactivate_supplier_id);
    $stmt->execute();
    logAction($user_data['user_id'], $user_data['username'], 'DELETE', 'Deactivated a supplier'); // Log the action here
    header('Location: view_suppliers.php');
    exit;
}
// Handle reactivating a supplier
if (isset($_GET['reactivate_supplier_id'])) {
    $reactivate_supplier_id = intval($_GET['reactivate_supplier_id']);
    $reactivate_sql = "UPDATE suppliers SET active = 1 WHERE supplier_id = ?";
    $stmt = $con->prepare($reactivate_sql);
    $stmt->bind_param('i', $reactivate_supplier_id);
    $stmt->execute();
    logAction($user_data['user_id'], $user_data['username'], 'UPDATE', 'Reactivated a supplier'); // Log the action here
    header('Location: view_suppliers.php');
    exit;
}

// Handle adding a new supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_supplier'])) {
    // Capture form data
    $supplier_name = $_POST['supplier_name'];
    $contact_name = $_POST['contact_name'];
    $contact_phone = $_POST['contact_phone'];
    $contact_email = $_POST['contact_email'];
    $address = $_POST['address'];

    // Insert the new supplier into the database
    $insert_supplier_sql = "INSERT INTO suppliers (supplier_name, contact_name, contact_phone, contact_email, address) 
                            VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($insert_supplier_sql);
    $stmt->bind_param('sssss', $supplier_name, $contact_name, $contact_phone, $contact_email, $address);

    if ($stmt->execute()) {
        logAction($user_data['user_id'], $user_data['username'], 'CREATE', 'Added a supplier'); // Log the action here
        header('Location: view_suppliers.php'); // Redirect to the suppliers page after successful addition
        exit;
    } else {
        $error_message = "Error adding supplier. Please try again.";
    }
}

// Define default sorting variables
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'supplier_id';
$sort_order = isset($_GET['sort_order']) && $_GET['sort_order'] === 'desc' ? 'desc' : 'asc';
$next_sort_order = $sort_order === 'asc' ? 'desc' : 'asc';

// Fetch suppliers from the database
$sql = "SELECT supplier_id, supplier_name, contact_name, contact_phone, contact_email, address, active FROM suppliers 
        ORDER BY $sort_column $sort_order";
$result = $con->query($sql);
if (!$result) {
    die("Error fetching suppliers: " . $con->error);
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers</title>
</head>
<body>
    <?php include('include/header.php')?>
    <div class="page-container">
        <h2 class="page-title">Suppliers</h2>
        <!-- Add Supplier Form -->
        <form action="view_suppliers.php" method="POST" class="adding-form">
            <h3>Add New Supplier</h3>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <div class="form-columns">
                <div class="left-column">
                    <!-- name -->
                    <div class="form-box">
                        <label for="supplier_name">Supplier Name:</label>
                        <input type="text" id="supplier_name" name="supplier_name" required>
                    </div>
                    <!-- con name -->
                    <div class="form-box">
                        <label for="contact_name">Contact Name:</label>
                        <input type="text" id="contact_name" name="contact_name" required>
                    </div>
                    <!-- con phone -->
                    <div class="form-box">
                        <label for="contact_phone">Contact Phone:</label>
                        <input type="text" id="contact_phone" name="contact_phone" required>
                    </div>
                </div>
                <div class="right-column">
                    <!-- con email -->
                    <div class="form-box">
                        <label for="contact_email">Contact Email:</label>
                        <input type="email" id="contact_email" name="contact_email" required>
                    </div>
                    <!-- address -->
                    <div class="form-box">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                </div>
            </div>
            <button type="submit" name="add_supplier" class="save-btn">Add Supplier</button>
        </form>
        <!-- Supplier Table -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th><a href="?sort_column=supplier_id&sort_order=<?php echo $next_sort_order; ?>">Supplier ID</a></th>
                        <th><a href="?sort_column=supplier_name&sort_order=<?php echo $next_sort_order; ?>">Supplier Name</a></th>
                        <th><a href="?sort_column=contact_name&sort_order=<?php echo $next_sort_order; ?>">Contact Name</a></th>
                        <th><a href="?sort_column=contact_phone&sort_order=<?php echo $next_sort_order; ?>">Contact Phone</a></th>
                        <th><a href="?sort_column=contact_email&sort_order=<?php echo $next_sort_order; ?>">Contact Email</a></th>
                        <th><a href="?sort_column=address&sort_order=<?php echo $next_sort_order; ?>">Address</a></th>
                        <th>Status</th>
                        <th>Manage Suppliers</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fill table rows with data from the query
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['supplier_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['supplier_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['contact_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['contact_phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['contact_email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                            echo "<td>" . ($row['active'] ? 'Active' : 'Inactive') . "</td>";
                            echo "<td><a href='manage_supplier.php?supplier_id=" . htmlspecialchars($row['supplier_id']) . "' class='manage-btn'>Manage Supplier</a></td>";
                            if ($row['active']) {
                                echo "<td><a href='view_suppliers.php?deactivate_supplier_id=" . htmlspecialchars($row['supplier_id']) . "' class='remove-btn' onclick='return confirm(\"Are you sure you want to deactivate this supplier?\");'>Deactivate Supplier</a></td>";
                            } else {
                                echo "<td><a href='view_suppliers.php?reactivate_supplier_id=" . htmlspecialchars($row['supplier_id']) . "' class='reactivate-btn' onclick='return confirm(\"Are you sure you want to reactivate this supplier?\");'>Reactivate Supplier</a></td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No suppliers found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>