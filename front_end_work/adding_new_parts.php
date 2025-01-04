<?php
include('include/init.php'); //initialise everything like user data

//adding new parts
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_part'])) {
    $part_name = $_POST['part_name'];
    $genre = $_POST['genre'];
    $manufacturer = $_POST['manufacturer'];
    $supplier_id = $_POST['supplier_id'];
    $unit_price = $_POST['unit_price'];
    $quantity_in_stock = $_POST['quantity_in_stock'];
    $reorder_level = $_POST['reorder_level'];
    $description = $_POST['description'];
    $branch_id = $_POST['branch_id'];

    //preparing SQL statement
    $stmt = $con->prepare("INSERT INTO parts (part_name, genre, manufacturer, supplier_id, unit_price, quantity_in_stock, reorder_level, description, branch_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiddisi", $part_name, $genre, $manufacturer, $supplier_id, $unit_price, $quantity_in_stock, $reorder_level, $description, $branch_id); // Binding params for preventing SQL attacks

    //execute and handle results
    if ($stmt->execute()) {
        //success, redirect to inventory.php
        logAction($user_data['user_id'], $user_data['username'], 'CREATE', "Added an item with part ID: $part_id"); // Log the action here

        header('Location: inventory.php');
        exit;
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

//fetch all suppliers for the dropdown
$supplier_sql = "SELECT supplier_id, supplier_name FROM suppliers";
$supplier_result = $con->query($supplier_sql);
$suppliers = [];
while ($row = $supplier_result->fetch_assoc()) {
    $suppliers[] = $row;
}

//fetch all branches for the dropdown
$branches_sql = "SELECT branch_id, branch_name FROM branches";
$branches_result = $con->query($branches_sql);
$branches = [];
while ($row = $branches_result->fetch_assoc()) {
    $branches[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding New Part</title>
    <link rel="stylesheet" href="style/new_part.css">
    <link rel="stylesheet" href="style/base.css">
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        
        <!-- Display Messages -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Add New Part Form -->
        <div class="add-part-container">
            <h3>Add New Part</h3>
            <form method="POST" id="add-part-form">
                <div class="form-group">
                    <label for="part_name">Part Name:</label>
                    <input type="text" id="part_name" name="part_name" required>
                </div>
                <div class="form-group">
                    <label for="genre">Type:</label>
                    <select id="genre" name="genre" required>
                        <option value="Engine">Engine</option>
                        <option value="Exhaust">Exhaust</option>
                        <option value="Body">Body</option>
                        <option value="Brakes">Brakes</option>
                        <option value="Transmission">Transmission</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Electrical">Electrical</option>
                        <option value="Cooling">Cooling</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Fuel">Fuel</option>
                        <option value="Interior">Interior</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="manufacturer">Manufacturer:</label>
                    <input type="text" id="manufacturer" name="manufacturer" required>
                </div>
                <div class="form-group">
                    <label for="supplier_id">Supplier ID:</label>
                    <select id="supplier_id" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo htmlspecialchars($supplier['supplier_id']); ?>">
                                <?php echo htmlspecialchars($supplier['supplier_id']) . ' - ' . htmlspecialchars($supplier['supplier_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="branch_id">Branch:</label>
                    <select id="branch_id" name="branch_id" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>">
                                <?php echo htmlspecialchars($branch['branch_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="unit_price">Unit Price:</label>
                    <input type="number" step="0.01" id="unit_price" name="unit_price" required>
                </div>
                <div class="form-group">
                    <label for="quantity_in_stock">Quantity in Stock:</label>
                    <input type="number" id="quantity_in_stock" name="quantity_in_stock" required>
                </div>
                <div class="form-group">
                    <label for="reorder_level">Reorder Level:</label>
                    <input type="number" id="reorder_level" name="reorder_level" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>
                <button type="submit" name="add_part" class="submit-btn">Add Part</button>
            </form>
        </div>
    </div>
</body>
</html>