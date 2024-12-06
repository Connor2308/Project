<?php
include('include/init.php')
//get the part_id from the url, default to 0 to avoid errors
$part_id = isset($_GET['part_id']) ? $_GET['part_id'] : 0;

//fetch the part details from the database
$sql = "SELECT * FROM invoices WHERE order_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $part_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $part = $result->fetch_assoc();
    $result->free(); //free the result to prevent "commands out of sync" error
} else {
    die("part not found.");
}

//handle the form submission for updating part details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //get form inputs
    $order_id = $_POST['order_id'];
    $part_name = $_POST['part_name'];
    $supplier_id = $_POST['supplier_id'];
    $unit_price = $_POST['unit_price']; 
    $quantity_in_stock = $_POST['quantity_in_stock']; 
    $description = $_POST['description'];
    $manufacturer = $_POST['manufacturer'];
    $genre = $_POST['genre']; 
    $reorder_level = $_POST['reorder_level']; 

    //validate that none of the required fields are empty
    if ( empty($order_id) ||empty($part_name) || empty($supplier_id) || empty($unit_price) || empty($quantity_in_stock) || empty($description) || empty($manufacturer) || empty($genre) || empty($reorder_level)) {
        die("all fields are required.");
    }

    //check if the new part name already exists in the database for a different part
    $check_part_name_sql = "SELECT COUNT(*) FROM invoices WHERE part_name = ? AND order_id != ?";
    $stmt_name = $con->prepare($check_part_name_sql);
    $stmt_name->bind_param('si', $part_name, $part_id); //correct binding: 's' for string, 'i' for integer
    $stmt_name->execute();
    $stmt_name->bind_result($name_exists);
    $stmt_name->fetch();
    $stmt_name->free_result(); //fix for potential sync errors

    if ($name_exists > 0) {
        die("part name already exists.");
    }

    //the 'bind_param' line needs to match the number of placeholders (?).
    $import_sql = "UPDATE invoices SET order_id = ?, part_name = ?, supplier_id = ?, unit_price = ?, quantity_in_stock = ?, genre = ?, manufacturer = ?, reorder_level = ? WHERE part_id = ?";
    $import_stmt = $con->prepare($import_sql);
    $impory_stmt->bind_param('sidssssi', 
        $order_id,       //order_id (integer)
        $part_name,      //part_name (string)
        $supplier_id,    //supplier_id (integer)
        $unit_price,     //unit_price (decimal)
        $quantity_in_stock, //quantity_in_stock (integer)
        $genre,          //genre (string)
        $manufacturer,   //manufacturer (string)
        $reorder_level,  //reorder_level (integer)
        $part_id         //part_id (integer)
    );

    $import_stmt->execute();

    //redirect to the parts table after saving changes
    header('Location: inventory.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">

    <link rel="stylesheet" href="style/signin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>  
    <?php include('include/header.php')?>
    <h2 class="page-title">Edit Part Details</h2>
    <div class="page-container">
        <form action="transications.php?part_id=<?php echo htmlspecialchars($part['part_id']); ?>" method="POST" class="manage-part-form">
            <div class="form-box">
                <label for="order_id">Order ID</label>
                <input type="text" id="order_id" name="order_id" value="<?php echo htmlspecialchars($part['order_id']);?>" required>
            </div>
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
                <label for="supplier_id">Supplier ID:</label>
                <select id="supplier_id" name="supplier_id" required>
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?php echo htmlspecialchars($supplier['supplier_id']); ?>" <?php echo ($part['supplier_id'] == $supplier['supplier_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($supplier['supplier_id']) . ' - ' . htmlspecialchars($supplier['supplier_name']); ?>
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

            <button type="submit">Create Invoice</button>
        </form>
    </div>
<?php include('include/footer.php')?>
</body>
</html>