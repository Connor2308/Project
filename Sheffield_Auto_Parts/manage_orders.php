<?php
include('include/init.php'); // Initialise, this includes the database connection
checkAdmin(); // Admin only page

// Get order ID from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details from the database
$sql = "SELECT orders.*, users.username FROM orders
        LEFT JOIN users ON orders.user_id = users.user_id
        WHERE orders.order_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $order = $result->fetch_assoc();
    $result->free(); // Prevent "Commands out of sync" error
} else {
    die("Order not found.");
}

// Handle form submission for updating order details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_date = $_POST['order_date'];
    $order_time = $_POST['order_time'];
    $total_cost = $_POST['total_cost'];
    $recipient = $_POST['recipient'];
    $order_status = $_POST['order_status'];

    // Update the order in the database
    $update_sql = "UPDATE orders 
                   SET order_date = ?, order_time = ?, total_cost = ?, order_status = ?, recipient_name = ? 
                   WHERE order_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param('ssdssi', $order_date, $order_time, $total_cost, $order_status, $recipient, $order_id);
    $update_stmt->execute();

    logAction($user_data['user_id'], $user_data['username'], 'UPDATE', 'Updated an order'); // Log the action here
    // Redirect back to the orders list after update
    header('Location: view_orders.php');
    exit;
}
?>
<!DOCTYPE html>
<!-- HTML -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Order</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <h2 class="page-title">Edit Order Details</h2>
        <!-- Back Button -->
        <div class="back-button-container">
            <a href="view_orders.php" class="back-btn">Back</a>
        </div>
        <form action="manage_orders.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" method="POST" class="form">
            <div class="form-columns">
                <div class="left-column">
                    <!-- order id -->
                    <div class="form-box">
                        <label for="order_id">Order ID:</label>
                        <input type="text" id="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>" readonly>
                    </div>
                    <!-- username -->
                    <div class="form-box">
                        <label for="username">Username:</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($order['username']); ?>" readonly>
                    </div>
                    <!-- recipient -->
                    <div class="form-box">
                        <label for="recipient">Recipient:</label>
                        <input type="text" id="recipient" name="recipient" value="<?php echo htmlspecialchars($order['recipient_name']); ?>" required>
                    </div>
                    <!-- order date -->
                    <div class="form-box">
                        <label for="order_date">Order Date:</label>
                        <input type="date" id="order_date" name="order_date" value="<?php echo htmlspecialchars($order['order_date']); ?>" required>
                    </div>
                </div>
                <div class="right-column">
                    <!-- order time -->
                    <div class="form-box">
                        <label for="order_time">Order Time:</label>
                        <input type="time" id="order_time" name="order_time" value="<?php echo htmlspecialchars($order['order_time']); ?>" required>
                    </div>
                    <!-- total cost -->
                    <div class="form-box">
                        <label for="total_cost">Total Cost:</label>
                        <input type="number" id="total_cost" name="total_cost" step="0.01" value="<?php echo htmlspecialchars($order['total_cost']); ?>" required>
                    </div>
                    <!-- order status -->
                    <div class="form-box">
                        <label for="order_status">Order Status:</label>
                        <select id="order_status" name="order_status" required>
                            <option value="Pending" <?php echo ($order['order_status'] === 'Pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="Completed" <?php echo ($order['order_status'] === 'Completed' ? 'selected' : ''); ?>>Completed</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
</body>
</html>