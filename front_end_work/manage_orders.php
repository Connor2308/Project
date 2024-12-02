<?php
include('include/init.php'); //initialise the application, including DB connection
checkAdmin(); //verify admin access

//get order ID from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

//fetch order details from the database
$sql = "SELECT orders.*, users.username FROM orders
        LEFT JOIN users ON orders.user_id = users.user_id
        WHERE orders.order_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $order = $result->fetch_assoc();
    $result->free(); //prevent "Commands out of sync" error
} else {
    die("Order not found.");
}

//handle form submission for updating order details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_date = $_POST['order_date'];
    $order_time = $_POST['order_time'];
    $total_cost = $_POST['total_cost'];
    $order_status = $_POST['order_status'];

    //update the order in the database
    $update_sql = "UPDATE orders 
                   SET order_date = ?, order_time = ?, total_cost = ?, order_status = ? 
                   WHERE order_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param('ssdsi', $order_date, $order_time, $total_cost, $order_status, $order_id);
    $update_stmt->execute();

    //redirect back to the orders list after update
    header('Location: view_orders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/orders.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Order</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <h2 class="page-title">Edit Order Details</h2>
    <div class="page-container">
        <form action="manage_orders.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" method="POST" class="manage-order-form">
            <div class="form-box">
                <label for="order_id">Order ID:</label>
                <input type="text" id="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>" readonly>
            </div>

            <div class="form-box">
                <label for="username">Username:</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($order['username']); ?>" readonly>
            </div>

            <div class="form-box">
                <label for="order_date">Order Date:</label>
                <input type="date" id="order_date" name="order_date" value="<?php echo htmlspecialchars($order['order_date']); ?>" required>
            </div>

            <div class="form-box">
                <label for="order_time">Order Time:</label>
                <input type="time" id="order_time" name="order_time" value="<?php echo htmlspecialchars($order['order_time']); ?>" required>
            </div>

            <div class="form-box">
                <label for="total_cost">Total Cost:</label>
                <input type="number" id="total_cost" name="total_cost" step="0.01" value="<?php echo htmlspecialchars($order['total_cost']); ?>" required>
            </div>

            <div class="form-box">
                <label for="order_status">Order Status:</label>
                <select id="order_status" name="order_status" required>
                    <option value="Pending" <?php echo ($order['order_status'] === 'Pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="Completed" <?php echo ($order['order_status'] === 'Completed' ? 'selected' : ''); ?>>Completed</option>
                </select>
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
