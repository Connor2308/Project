<?php
include('include/init.php'); 
checkAdmin();

// Handle the form submission for updating the total paid in the invoices table
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $total_paid = isset($_POST['total_paid']) ? floatval($_POST['total_paid']) : 0.00;

    // Fetch the total cost from the orders table
    $order_sql = "SELECT total_cost FROM orders WHERE order_id = ?";
    $order_stmt = $con->prepare($order_sql);
    $order_stmt->bind_param('i', $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();

    if ($order_result->num_rows != 1) {
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit;
    }

    $order = $order_result->fetch_assoc();
    $total_cost = $order['total_cost'];

    if ($order_id > 0 && $total_paid >= 0 && $total_paid <= $total_cost) {
        // Update the total paid in the invoices table
        $update_invoice_sql = "UPDATE invoices SET total_paid = ? WHERE order_id = ?";
        $update_invoice_stmt = $con->prepare($update_invoice_sql);
        $update_invoice_stmt->bind_param('di', $total_paid, $order_id);

        if ($update_invoice_stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update invoice.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input. Total paid must be between 0 and the total cost.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>