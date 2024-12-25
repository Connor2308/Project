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

    //preparing SQL statement
    $stmt = $con->prepare("INSERT INTO parts (part_name, genre, manufacturer, supplier_id, unit_price, quantity_in_stock, reorder_level, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiddis", $part_name, $genre, $manufacturer, $supplier_id, $unit_price, $quantity_in_stock, $reorder_level, $description); // Binding params for preventing SQL attacks

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


        </div>
    </div>
</body>
</html>
