<?php
include('include/init.php'); 
checkAdmin();

// Get the order_id from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$order_sql = "SELECT 
                orders.order_id,
                orders.user_id,
                orders.order_date,
                orders.order_time,
                orders.total_cost,
                orders.order_status,
                orders.recipient_name,
                users.username
              FROM 
                orders
              LEFT JOIN 
                users 
              ON 
                orders.user_id = users.user_id
              WHERE 
                orders.order_id = ?";
$order_stmt = $con->prepare($order_sql);
$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows != 1) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

// Fetch invoice details
$invoice_sql = "SELECT * FROM invoices WHERE order_id = ?";
$invoice_stmt = $con->prepare($invoice_sql);
$invoice_stmt->bind_param('i', $order_id);
$invoice_stmt->execute();
$invoice_result = $invoice_stmt->get_result();

if ($invoice_result->num_rows != 1) {
    die("Invoice not found.");
}

$invoice = $invoice_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/invoice.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoice</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/update_invoice.js"></script>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <h2 class="page-title">Invoice for Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h2>

        <!-- Order Details -->
        <div class="order-details">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
            <p><strong>Order Time:</strong> <?php echo htmlspecialchars($order['order_time']); ?></p>
            <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
            <p><strong>Recipient Name:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
            <p><strong>Total Cost:</strong> £<span id="total_cost"><?php echo number_format($order['total_cost'], 2, '.', ','); ?></span></p>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <h3>Invoice Details</h3>
            <form id="invoice-form" method="POST">
                <input type="hidden" id="order_id" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                <p><strong>Total Paid:</strong> £<input type="number" step="0.01" name="total_paid" id="total_paid" value="<?php echo htmlspecialchars($invoice['total_paid']); ?>" required></p>
                <p><strong>Total Cost:</strong> £<?php echo number_format($order['total_cost'], 2, '.', ','); ?></p>
                <button type="submit" name="update_invoice">Update Invoice</button>
            </form>
            <a href="generate_invoice_pdf.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" class="pdf-btn">Generate PDF</a>
        </div>

        <!-- Order Items -->
        <div class="order-items">
            <h3>Order Items</h3>
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Part Name</th>
                        <th>Quantity</th>
                        <th>Collective Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch order items
                    $items_sql = "SELECT 
                                    order_items.order_item_id,
                                    parts.part_name,
                                    order_items.order_quantity,
                                    order_items.order_price
                                  FROM 
                                    order_items
                                  LEFT JOIN 
                                    parts 
                                  ON 
                                    order_items.part_id = parts.part_id
                                  WHERE 
                                    order_items.order_id = ?";
                    $items_stmt = $con->prepare($items_sql);
                    $items_stmt->bind_param('i', $order_id);
                    $items_stmt->execute();
                    $items_result = $items_stmt->get_result();

                    if ($items_result->num_rows > 0) {
                        while ($item = $items_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($item['order_item_id'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($item['part_name'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($item['order_quantity'] ?? '') . "</td>";
                            echo "<td>£" . number_format($item['order_price'] ?? 0, 2, '.', ',') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No items found for this order.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>