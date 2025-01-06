<?php
include('include/init.php'); // Initialise, includes the database connection
checkAdmin(); // Verifying admin

// Handle the form submission for adding a new part
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

    // Insert the new part into the database
    $insert_sql = "INSERT INTO parts (part_name, supplier_id, branch_id, unit_price, quantity_in_stock, description, manufacturer, genre, reorder_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $con->prepare($insert_sql);
    $insert_stmt->bind_param('siidssssi', $part_name, $supplier_id, $branch_id, $unit_price, $quantity_in_stock, $description, $manufacturer, $genre, $reorder_level);
    $insert_stmt->execute();

    logAction($user_data['user_id'], $user_data['username'], 'INSERT', "Added a new part"); // Log the action here
    // Redirect to the parts table after adding the new part
    header('Location: inventory.php');
    exit;
}

// Fetch all genres for the dropdown
$genre_sql = "SELECT DISTINCT genre FROM parts"; //here is where is learned about distinct www.w3schools.com/sql/sql_distinct.asp
$genre_result = $con->query($genre_sql);
$genres = [];
while ($row = $genre_result->fetch_assoc()) { //loop for each genre
    $genres[] = $row['genre'];
}

// Fetch only active suppliers for the dropdown, as we dont want them adding inactive suppliers to new parts
$supplier_sql = "SELECT supplier_id, supplier_name FROM suppliers WHERE active = 1";
$supplier_result = $con->query($supplier_sql);
$suppliers = [];
while ($row = $supplier_result->fetch_assoc()) {
    $suppliers[] = $row;
}

// Fetch all branches for the dropdown, it will only show active branches that are available
$branch_sql = "SELECT branch_id, branch_name FROM branches WHERE active = 1";
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
    <title>Add New Part</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <h2 class="page-title">Add New Part</h2>
            <!-- Back Button -->
        <div class="back-button-container">
            <a href="inventory.php" class="back-btn">Back</a>
        </div>
        <form action="adding_new_parts.php" method="POST" class="adding-form">
            <div class="form-columns">
                <div class="left-column">
                    <!-- Part Name -->
                    <div class="form-box">
                        <label for="part_name">Part Name:</label>
                        <input type="text" id="part_name" name="part_name" required>
                    </div>
                    <!-- Genre -->
                    <div class="form-box">
                        <label for="genre">Genre:</label>
                        <select id="genre" name="genre" required>
                            <option value="">Select Genre</option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo htmlspecialchars($genre); ?>"><?php echo htmlspecialchars($genre); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Manufac -->
                    <div class="form-box">
                        <label for="manufacturer">Manufacturer:</label>
                        <input type="text" id="manufacturer" name="manufacturer" required>
                    </div>
                    <!-- Supplier -->
                    <div class="form-box">
                        <label for="supplier_id">Supplier:</label>
                        <select id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo htmlspecialchars($supplier['supplier_id']); ?>"><?php echo htmlspecialchars($supplier['supplier_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="right-column">
                    <!-- Branch -->
                    <div class="form-box">
                        <label for="branch_id">Branch:</label>
                        <select id="branch_id" name="branch_id" required>
                            <option value="">Select Branch</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>"><?php echo htmlspecialchars($branch['branch_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Unit Price -->
                    <div class="form-box">
                        <label for="unit_price">Unit Price:</label>
                        <input type="number" step="0.01" id="unit_price" name="unit_price" required>
                    </div>
                    <!-- Quantity in Stock -->
                    <div class="form-box">
                        <label for="quantity_in_stock">Quantity in Stock:</label>
                        <input type="number" id="quantity_in_stock" name="quantity_in_stock" required>
                    </div>
                    <!-- Reorder Level -->
                    <div class="form-box">
                        <label for="reorder_level">Reorder Level:</label>
                        <input type="number" id="reorder_level" name="reorder_level" required>
                    </div>
                    <!-- Description -->
                    <div class="form-box">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="save-btn">Add Part</button>
        </form>
    </div>
</body>
</html>