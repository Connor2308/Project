<?php
// Include your database connection file
include('include/init.php');

// Get the data from the AJAX request
$invoice_id = isset($_POST['invoice_id']) ? $_POST['invoice_id'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : null;

// Validate input
if ($invoice_id && $action && in_array($action, ['increase', 'decrease'])) {
    try {
        // Begin transaction to ensure data consistency
        $con->begin_transaction();

        // Get current quantity or total due (depending on your need)
        $query = "SELECT total_due FROM invoices WHERE invoice_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $invoice_id);
        $stmt->execute();
        $stmt->bind_result($total_due);
        $stmt->fetch();
        $stmt->close();

        // Modify total_due based on the action
        $quantity_change = ($action == 'increase') ? 10 : -10;  // For example, adjust the stock by 10 units (you can adjust this logic)
        $new_total_due = $total_due + $quantity_change;

        // Update the database with the new total_due
        $update_query = "UPDATE invoices SET total_due = ? WHERE invoice_id = ?";
        $update_stmt = $con->prepare($update_query);
        $update_stmt->bind_param('di', $new_total_due, $invoice_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Commit the transaction
        $con->commit();

        // Return the updated quantity or total_due (or whatever value you wish to return)
        echo number_format($new_total_due, 2);  // Format the updated total due as a currency string
    } catch (Exception $e) {
        // Rollback if there's an error
        $con->rollback();
        echo 'Error updating stock: ' . $e->getMessage();
    }
} else {
    echo 'Invalid request';
}
?>
