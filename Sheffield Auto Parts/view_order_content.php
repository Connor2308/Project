<?php
include('include/init.php');

//getting the order ID from the URL
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    //redirect if no order ID is provided
    header('Location: view_orders.php');
    exit;
}

//SQL Query to get the order items details for the specific order
$sql = "SELECT 
            order_items.order_item_id,
            order_items.order_id,
            order_items.part_id,
            order_items.order_quantity,
            order_items.order_price,
            parts.part_name,
            parts.unit_price
        FROM 
            order_items
        LEFT JOIN 
            parts 
        ON 
            order_items.part_id = parts.part_id
        WHERE 
            order_items.order_id = ?";
        
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $order_id); //binding the order_id parameter to the query
$stmt->execute();
$result = $stmt->get_result(); //execute the query and get the result this is to keep us safe from sql injections!

//storing the total price, we got to initalise a variable to store the total price
$total_order_price = 0;

//handle form submission to add a new part to the order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_part'])) {
    $part_id = $_POST['part_id'];
    $quantity = $_POST['order_quantity'];

    //validate quantity (must be a positive integer)
    if (is_numeric($quantity) && $quantity > 0) {
        //SQL to insert the new part into the order
        $insert_sql = "INSERT INTO order_items (order_id, part_id, order_quantity, order_price) 
                        SELECT ?, ?, ?, unit_price 
                        FROM parts WHERE part_id = ?";
        
        $insert_stmt = $con->prepare($insert_sql);
        $insert_stmt->bind_param('iiii', $order_id, $part_id, $quantity, $part_id);//bind param is iiii as it is 4 intergers
        if ($insert_stmt->execute()) {
            //after inserting, update the total cost in orders table
            updateOrderTotal($con, $order_id);
            logAction($user_data['user_id'], $user_data['username'], 'ADD', "Added an item with part ID: $part_id to order ID: $order_id"); // Log the action here

            //redirect to the current order details page after inserting the new part
            header("Location: view_order_content.php?order_id=$order_id");
            exit;
        } else {
            echo "<p>Error adding part to order. Please try again.</p>";
        }
    } else {
        echo "<p>Please enter a valid quantity.</p>";
    }
}

//handle the form submission to remove a part from the order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_part'])) {
    $order_item_id = $_POST['order_item_id'];

    //SQL Query to remove the item from the order
    $remove_sql = "DELETE FROM order_items WHERE order_item_id = ?";
    $remove_stmt = $con->prepare($remove_sql);
    $remove_stmt->bind_param('i', $order_item_id);

    if ($remove_stmt->execute()) {
        //after removing, update the total cost in orders table
        updateOrderTotal($con, $order_id);
        //redirect to the current order details page after removing the part
        logAction($user_data['user_id'], $user_data['username'], 'DELETE', "Removed an item with part ID: $part_id from order ID: $order_id"); // Log the action here
        header("Location: view_order_content.php?order_id=$order_id");
        exit;
    } else {
        echo "<p>Error removing part from order. Please try again.</p>";
    }
}

//fetch all available parts for the dropdown 
$parts_query = "SELECT part_id, part_name, manufacturer, unit_price FROM parts";
$parts_result = $con->query($parts_query);

?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Items - Order #<?php echo htmlspecialchars($order_id); ?></title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <!-- Back Button -->
        <div class="back-button-container">
            <a href="view_orders.php" class="back-btn">Back</a>
        </div>
        <h2 class="page-title">Order #<?php echo htmlspecialchars($order_id); ?> - Items</h2>  
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Part Name</th>
                        <th>Quantity</th>
                        <th>Individual Price</th>
                        <th>Total Price of Items</th>
                        <th>Remove From Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //display order items in table rows
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $total_price = $row['order_quantity'] * $row['unit_price']; //calculate total price for each item
                            $total_order_price += $total_price; //adding item total to the overall total

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['order_item_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['part_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['order_quantity']) . "</td>";
                            echo "<td>£" . htmlspecialchars($row['unit_price']) . "</td>";
                            echo "<td>£" . htmlspecialchars($total_price) . "</td>"; //display total price for item?>
                            <td>
                                <!-- Remove from Order Button -->
                                <form method="POST" action="">
                                    <input type="hidden" name="order_item_id" value="<?php echo $row['order_item_id']; ?>">
                                    <button type="submit" name="remove_part" class="remove-btn">Remove</button>
                                </form>
                            </td>
                            <?php
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No items found for this order.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Form to Add a New Part to the Order -->
        <div class="add-part-form">
            <h3>Add a New Part to the Order</h3>
            <form method="POST">
                <label for="part_id">Select Part:</label>
                <select name="part_id" id="part_id" required>
                    <option value="" disabled selected>Select a Part</option>
                    <?php
                    //Populate dropdown with available parts
                    while ($part = $parts_result->fetch_assoc()) {
                        echo "<option value='" . $part['part_id'] . "'>" . 
                             htmlspecialchars($part['part_id']) . " - " . 
                             htmlspecialchars($part['part_name']) . " (" . 
                             htmlspecialchars($part['manufacturer']) . ") - £" . 
                             htmlspecialchars($part['unit_price']) . 
                             "</option>";
                    }
                    ?>
                </select>
                <label for="order_quantity">Quantity:</label>
                <input type="number" name="order_quantity" id="order_quantity" required min="1">

                <button type="submit" name="add_part">Add Part</button>
            </form>
        </div>

        <!-- Total price of the order -->
        <div class="total-price">
            <h3>Total Order Price: £<?php echo htmlspecialchars($total_order_price); ?></h3>
        </div>
    </div>
</body>
</html>
