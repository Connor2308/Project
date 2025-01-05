<?php
include('include/init.php'); // Initialise, includes the database connection
checkAdmin(); // Verifying admin

// Get the part_id from the URL, default to 0 to avoid errors
$part_id = isset($_GET['part_id']) ? $_GET['part_id'] : 0;

// Fetch the part details from the database
$sql = "SELECT * FROM parts WHERE part_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $part_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $part = $result->fetch_assoc();
    $result->free(); // Free the result to prevent "commands out of sync" error
} else {
    die("Part not found.");
}

// Handle the form submission for updating part details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $part_name = $_POST['part_name'];
    $supplier_id = $_POST['supplier_id'];
    $branch_id = $_POST['branch_id'];
    $unit_price = $_POST['unit_price']; 
    $quantity_in_stock = $_POST['quantity_in_stock']; 
    $description = $_POST['description'];
    $manufacturer = $_POST['manufacturer'];
    $genre = $_POST['genre']; 
    $reorder_level = $_POST['reorder_level']; 

    // Validate that none of the required fields are empty
    if (empty($part_name) || empty($supplier_id) || empty($branch_id) || empty($unit_price) || empty($quantity_in_stock) || empty($description) || empty($manufacturer) || empty($genre) || empty($reorder_level)) {
        die("All fields are required.");
    }

    // Check if the new part name already exists in the database for a different part
    $check_part_name_sql = "SELECT COUNT(*) FROM parts WHERE part_name = ? AND part_id != ?";
    $stmt_name = $con->prepare($check_part_name_sql);
    $stmt_name->bind_param('si', $part_name, $part_id); // Correct binding: 's' for string, 'i' for integer
    $stmt_name->execute();
    $stmt_name->bind_result($name_exists);
    $stmt_name->fetch();
    $stmt_name->free_result(); // Fix for potential sync errors

    if ($name_exists > 0) {
        die("Part name already exists.");
    }

    // The 'bind_param' line needs to match the number of placeholders (?).
    $update_sql = "UPDATE parts SET part_name = ?, supplier_id = ?, branch_id = ?, unit_price = ?, quantity_in_stock = ?, genre = ?, manufacturer = ?, reorder_level = ? WHERE part_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param('siidssssi', 
        $part_name,      // part_name (string)
        $supplier_id,    // supplier_id (integer)
        $branch_id,      // branch_id (integer)
        $unit_price,     // unit_price (decimal)
        $quantity_in_stock, // quantity_in_stock (integer)
        $genre,          // genre (string)
        $manufacturer,   // manufacturer (string)
        $reorder_level,  // reorder_level (integer)
        $part_id         // part_id (integer)
    );

    $update_stmt->execute();

    logAction($user_data['user_id'], $user_data['username'], 'UPDATE', "Updated Part ID: $part_id"); // Log the action here
    // Redirect to the parts table after saving changes
    header('Location: inventory.php');
    exit;
}

// Fetch all genres for the dropdown
$genre_sql = "SELECT DISTINCT genre FROM parts";
$genre_result = $con->query($genre_sql);
$genres = [];
while ($row = $genre_result->fetch_assoc()) {
    $genres[] = $row['genre'];
}

// Fetch only active suppliers for the dropdown
$supplier_sql = "SELECT supplier_id, supplier_name FROM suppliers WHERE active = 1";
$supplier_result = $con->query($supplier_sql);
$suppliers = [];
while ($row = $supplier_result->fetch_assoc()) {
    $suppliers[] = $row;
}

// Fetch all branches for the dropdown
$branch_sql = "SELECT branch_id, branch_name FROM branches";
$branch_result = $con->query($branch_sql);
$branches = [];
while ($row = $branch_result->fetch_assoc()) {
    $branches[] = $row;
}
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Part</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <h2 class="page-title">Edit Part Details</h2>
        <!-- Container that contains all of the boxes -->
        <div class="form-container">
            <!-- Back Button -->
            <div class="back-button-container">
                <a href="inventory.php" class="back-btn">Back</a>
            </div>
            <form action="manage_part.php?part_id=<?php echo htmlspecialchars($part['part_id']); ?>" method="POST" class="form">
                <div class="form-columns">
                    <div class="left-column">
                        <div class="form-box">
                            <label for="part_name">Part Name:</label>
                            <input type="text" id="part_name" name="part_name" value="<?php echo htmlspecialchars($part['part_name']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="genre">Genre:</label>
                            <select id="genre" name="genre" required>
                                <option value="">Select Genre</option>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?php echo htmlspecialchars($genre); ?>" <?php echo ($part['genre'] == $genre) ? 'selected' : ''; ?>><?php echo htmlspecialchars($genre); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-box">
                            <label for="manufacturer">Manufacturer:</label>
                            <input type="text" id="manufacturer" name="manufacturer" value="<?php echo htmlspecialchars($part['manufacturer']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="supplier_id">Supplier:</label>
                            <select id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo htmlspecialchars($supplier['supplier_id']); ?>" <?php echo ($part['supplier_id'] == $supplier['supplier_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="right-column">
                        <div class="form-box">
                            <label for="branch_id">Branch:</label>
                            <select id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>" <?php echo ($part['branch_id'] == $branch['branch_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($branch['branch_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-box">
                            <label for="unit_price">Unit Price:</label>
                            <input type="number" step="0.01" id="unit_price" name="unit_price" value="<?php echo htmlspecialchars($part['unit_price']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="quantity_in_stock">Quantity in Stock:</label>
                            <input type="number" id="quantity_in_stock" name="quantity_in_stock" value="<?php echo htmlspecialchars($part['quantity_in_stock']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="reorder_level">Reorder Level:</label>
                            <input type="number" id="reorder_level" name="reorder_level" value="<?php echo htmlspecialchars($part['reorder_level']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" required><?php echo htmlspecialchars($part['description']); ?></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="save-btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>