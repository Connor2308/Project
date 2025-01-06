<?php
include('include/init.php'); // Initialise, includes the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_id = isset($_POST['part_id']) ? intval($_POST['part_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($part_id > 0 && ($action === 'increase' || $action === 'decrease')) {
        // Fetch the current quantity
        $sql = "SELECT quantity_in_stock FROM parts WHERE part_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $part_id);
        $stmt->execute();
        $stmt->bind_result($quantity_in_stock);
        $stmt->fetch();
        $stmt->close();

        // Update the quantity based on the action
        if ($action === 'increase') {
            $quantity_in_stock++;
        } elseif ($action === 'decrease' && $quantity_in_stock > 0) {
            $quantity_in_stock--;
        }

        // Update the quantity in the database
        $update_sql = "UPDATE parts SET quantity_in_stock = ? WHERE part_id = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param('ii', $quantity_in_stock, $part_id);
        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'quantity_in_stock' => $quantity_in_stock]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update quantity.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>