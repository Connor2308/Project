<?php
include('include/init.php'); 
checkAdmin();
//Sorting Section
$sort_column = $_GET['sort_column'] ?? 'order_id'; 
$sort_order = $_GET['sort_order'] ?? 'ASC';
$next_sort_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC'; 

//SQL Query for orders
$sql = "SELECT 
            orders.order_id,
            orders.user_id,
            orders.order_date,
            orders.order_time,
            orders.total_cost,
            orders.order_status,
            users.username
        FROM 
            orders
        LEFT JOIN 
            users 
        ON 
            orders.user_id = users.user_id
        ORDER BY 
            $sort_column $sort_order";

$result = $con->query($sql);

//handle form submission for creating a new order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_order'])) {
    //capture form data
    $user_id = $_POST['user_id'];
    $order_status = $_POST['order_status'];
    $order_date = $_POST['order_date'];
    $order_time = $_POST['order_time'];

    //insert the new order into the orders table
    $insert_sql = "INSERT INTO orders (user_id, order_date, order_time, order_status) 
                   VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($insert_sql);
    $stmt->bind_param('isss', $user_id, $order_date, $order_time, $order_status);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; //get the inserted order's ID
        //redirect to the manage orders page with the newly created order's ID
        header("Location: view_orders.php?order_id=$order_id");
        exit;
    } else {
        echo "<p>Error creating order. Please try again.</p>";
    }
}

//get all users for the user selection dropdown
$users_query = "SELECT user_id, username FROM users";
$users_result = $con->query($users_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_order'])) {
    $order_id = $_POST['order_id'];

    //begin transaction to ensure data consistency
    $con->begin_transaction();

    try {
        //Delete all items in the order
        $delete_items_sql = "DELETE FROM order_items WHERE order_id = ?";
        $delete_items_stmt = $con->prepare($delete_items_sql);
        $delete_items_stmt->bind_param('i', $order_id);
        if (!$delete_items_stmt->execute()) {
            throw new Exception('Error deleting order items.');
        }

        //Delete the order itself
        $delete_order_sql = "DELETE FROM orders WHERE order_id = ?";
        $delete_order_stmt = $con->prepare($delete_order_sql);
        $delete_order_stmt->bind_param('i', $order_id);
        if (!$delete_order_stmt->execute()) {
            throw new Exception('Error deleting the order.');
        }

        //Commit the transaction
        $con->commit();
        header('Location: view_orders.php'); //Redirect to orders page after successful removal
        exit;
    } catch (Exception $e) {
        //Rollback in case of error
        $con->rollback();
        echo "<p>Error removing order: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Orders</title>
    <script>
        //Automatically set the order date and time based on user's computer time
        window.onload = function() {
            const dateField = document.getElementById('order_date');
            const timeField = document.getElementById('order_time');
            const currentDate = new Date();

            //setting the current date in YYYY-MM-DD format
            dateField.value = currentDate.toISOString().split('T')[0];

            //setting the current time in HH:MM format
            let hours = currentDate.getHours();
            let minutes = currentDate.getMinutes();
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            timeField.value = hours + ':' + minutes;
        };
    </script>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <h2 class="page-title">System's Orders</h2>

        <!-- Table to View Existing Orders -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <!-- Table headers with sorting functionality -->
                        <th><a href="?sort_column=order_id&sort_order=<?php echo $next_sort_order; ?>">Order ID</a></th>
                        <th><a href="?sort_column=user_id&sort_order=<?php echo $next_sort_order; ?>">User ID</a></th>
                        <th><a href="?sort_column=username&sort_order=<?php echo $next_sort_order; ?>">Username</a></th>
                        <th><a href="?sort_column=order_date&sort_order=<?php echo $next_sort_order; ?>">Order Date</a></th>
                        <th><a href="?sort_column=order_time&sort_order=<?php echo $next_sort_order; ?>">Order Time</a></th>
                        <th><a href="?sort_column=order_status&sort_order=<?php echo $next_sort_order; ?>">Status</a></th>
                        <th>Total Cost</th>
                        <th>Manage Orders</th>
                        <th>View Order Details</th>
                        <th>Remove Order</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //fill table rows with data from the query
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            //format the total cost to show as currency
                            $total_cost = number_format($row['total_cost'], 2, '.', ','); 
                            echo "<tr id='row-{$row['order_id']}'>";
                            echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username'] ?? "N/A") . "</td>";
                            echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['order_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['order_status']) . "</td>";
                            echo "<td>£" . $total_cost . "</td>"; // Display total cost
                            // Manage Order button
                            echo "<td><a href='manage_orders.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='manage-btn'>Manage Order</a></td>";
                            // View Order Details button
                            echo "<td><a href='view_order_details.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='manage-btn'>View Order Details</a></td>";
                            // Remove Order button
                            echo "<td>
                                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this order?\");'>
                                        <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                                        <button type='submit' name='remove_order' class='remove-btn'>Remove Order</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Form to Create New Order -->
        <div class="create-order-form">
            <h3>Create New Order</h3>
            <form method="POST">
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id" required>
                    <option value="" disabled selected>Select a User</option>
                    <?php
                    //populate dropdown with available users
                    while ($user = $users_result->fetch_assoc()) {
                        echo "<option value='" . $user['user_id'] . "'>" . htmlspecialchars($user['username']) . "</option>";
                    }
                    ?>
                </select>

                <label for="order_date">Order Date:</label>
                <input type="date" name="order_date" id="order_date" required>

                <label for="order_time">Order Time:</label>
                <input type="time" name="order_time" id="order_time" required>

                <label for="order_status">Order Status:</label>
                <select name="order_status" id="order_status" required>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                </select>

                <button type="submit" name="create_order">Create Order</button>
            </form>
        </div>
    </div>
</body>
</html>
