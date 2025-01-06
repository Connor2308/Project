<?php
include('include/init.php'); // Initialise, this includes the database connection
checkAdmin(); // Verify admin access

// Getting supplier id from the URL, defaults to 0 to ensure it doesn't break
$supplier_id = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : 0;

// Fetching supplier details from the database
$sql = "SELECT * FROM suppliers WHERE supplier_id = ? AND active = 1"; // Ensuring only active suppliers
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $supplier_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $supplier = $result->fetch_assoc();
    $result->free();  // Free the result to prevent "Commands out of sync" error
} else {
    die("Supplier not found.");
}

// Handling the form submission when the "Save Changes" button is pressed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = $_POST['supplier_name'];
    $contact_name = $_POST['contact_name'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    $address = $_POST['address'];
    $active = $_POST['active']; // Active/Inactive

    // Check if the supplier name already exists
    $check_supplier_sql = "SELECT COUNT(*) FROM suppliers WHERE supplier_name = ? AND supplier_id != ?";
    $stmt_supplier = $con->prepare($check_supplier_sql);
    $stmt_supplier->bind_param('si', $supplier_name, $supplier_id);
    $stmt_supplier->execute();
    $stmt_supplier->bind_result($supplier_exists);
    $stmt_supplier->fetch();
    $stmt_supplier->free_result(); // Free the result

    if ($supplier_exists > 0) {
        die("Supplier name already exists.");
    }

    // Updating the supplier's information
    $update_sql = "UPDATE suppliers SET supplier_name = ?, contact_name = ?, contact_email = ?, contact_phone = ?, address = ?, active = ? WHERE supplier_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param('ssssssi', $supplier_name, $contact_name, $contact_email, $contact_phone, $address, $active, $supplier_id);
    $update_stmt->execute();

    logAction($user_data['user_id'], $user_data['username'], 'UPDATE', 'Updated a supplier'); // Log the action here
    // Once the action is completed, redirect to the suppliers list page
    header('Location: view_suppliers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supplier</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <h2 class="page-title">Edit Supplier Details</h2>
        <!-- Back Button -->
        <div class="back-button-container">
            <a href="view_suppliers.php" class="back-btn">Back</a>
        </div>
        <form action="manage_supplier.php?supplier_id=<?php echo htmlspecialchars($supplier['supplier_id']); ?>" method="POST" class="form">
            <div class="form-columns">
                <div class="left-column">
                    <!-- name -->
                    <div class="form-box">
                        <label for="supplier_name">Supplier Name:</label>
                        <input type="text" id="supplier_name" name="supplier_name" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required>
                    </div>
                    <!-- contact name -->
                    <div class="form-box">
                        <label for="contact_name">Contact Name:</label>
                        <input type="text" id="contact_name" name="contact_name" value="<?php echo htmlspecialchars($supplier['contact_name']); ?>" required>
                    </div>
                    <!-- con email -->
                    <div class="form-box">
                        <label for="contact_email">Contact Email:</label>
                        <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($supplier['contact_email']); ?>" required>
                    </div>
                    <!-- con phone -->
                    <div class="form-box">
                        <label for="contact_phone">Contact Phone:</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($supplier['contact_phone']); ?>" required>
                    </div>
                </div>
                <div class="right-column">
                    <!-- address -->
                    <div class="form-box">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" required><?php echo htmlspecialchars($supplier['address']); ?></textarea>
                    </div>
                    <!-- are they still an active supplier -->
                    <div class="form-box">
                        <label for="active">Active:</label>
                        <select id="active" name="active" required>
                            <option value="1" <?php echo ($supplier['active'] == '1' ? 'selected' : ''); ?>>Active</option>
                            <option value="0" <?php echo ($supplier['active'] == '0' ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
</body>
</html>