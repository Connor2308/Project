<?php
include('include/init.php'); 

//sorting section
$sort_column = $_GET['sort_column'] ?? 'order_id'; 
$sort_order = $_GET['sort_order'] ?? 'ASC';
$next_sort_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC'; 

//sql query for orders
$sql = "SELECT 
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
        ORDER BY 
            $sort_column $sort_order";

$result = $con->query($sql);

//create an order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_order'])) {
    // capture form data
    $user_id = $_POST['user_id'] ?? null;
    $order_status = $_POST['order_status'] ?? null;
    $order_date = $_POST['order_date'] ?? null;
    $order_time = $_POST['order_time'] ?? null;
    $recipient_name = $_POST['recipient_name'] ?? null;

    if ($user_id && $order_status && $order_date && $order_time && $recipient_name) {
        // begin transaction
        $con->begin_transaction();

        try {
            // insert the new order into the orders table
            $insert_sql = "INSERT INTO orders (user_id, order_date, order_time, order_status, recipient_name) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($insert_sql);
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $con->error);
            }
            $stmt->bind_param('issss', $user_id, $order_date, $order_time, $order_status, $recipient_name);
            if (!$stmt->execute()) {
                throw new Exception("Execute statement failed: " . $stmt->error);
            }
            $order_id = $stmt->insert_id; // get the inserted order's ID

            // insert the new invoice into the invoices table
            $invoice_date = $order_date;
            $invoice_time = $order_time;
            $total_due = 0.00; // assuming initial total due is 0.00
            $status = 0; // assuming 0 means unpaid
            $total_paid = 0.00;

            $invoice_sql = "INSERT INTO invoices (order_id, invoice_date, invoice_time, total_due, status, total_paid) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $invoice_stmt = $con->prepare($invoice_sql);
            if (!$invoice_stmt) {
                throw new Exception("Prepare statement failed: " . $con->error);
            }
            $invoice_stmt->bind_param('issdii', $order_id, $invoice_date, $invoice_time, $total_due, $status, $total_paid);
            if (!$invoice_stmt->execute()) {
                throw new Exception("Execute statement failed: " . $invoice_stmt->error);
            }

            // commit the transaction
            $con->commit();

            logAction($user_data['user_id'], $user_data['username'], 'CREATE', 'Added an order'); // log the action here

            // redirect to the manage orders page with the newly created order's ID
            header("Location: view_orders.php?order_id=$order_id");
            exit;
        } catch (Exception $e) {
            // rollback transaction in case of error
            $con->rollback();
            echo "<p>Error creating order: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Error: All fields are required.</p>";
    }
}

//get all users for the user selection dropdown
$users_query = "SELECT user_id, username FROM users";
$users_result = $con->query($users_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_order'])) {
    $order_id = $_POST['order_id'];

    //begin transaction to ensure data consistency, rollback if error occurs
    $con->begin_transaction();
    try {
        //delete all references of the order in the invoice table
        $delete_invoice_sql = "DELETE FROM invoices WHERE order_id = ?";
        $delete_invoice_stmt = $con->prepare($delete_invoice_sql);
        $delete_invoice_stmt->bind_param('i', $order_id);
        if (!$delete_invoice_stmt->execute()) {
            throw new Exception('Error deleting invoices.');
        }

        //delete all items in the order
        $delete_items_sql = "DELETE FROM order_items WHERE order_id = ?";
        $delete_items_stmt = $con->prepare($delete_items_sql);
        $delete_items_stmt->bind_param('i', $order_id);
        if (!$delete_items_stmt->execute()) {
            throw new Exception('Error deleting order items.');
        }

        //delete the order itself
        $delete_order_sql = "DELETE FROM orders WHERE order_id = ?";
        $delete_order_stmt = $con->prepare($delete_order_sql);
        $delete_order_stmt->bind_param('i', $order_id);
        if (!$delete_order_stmt->execute()) {
            throw new Exception('Error deleting the order.');
        }

        //commit the transaction
        logAction($user_data['user_id'], $user_data['username'], 'DELETE', 'Removed an order'); // log the action here
        $con->commit();
        header('Location: view_orders.php'); //redirect to orders page after successful removal
        exit;
    } catch (Exception $e) {
        //rollback in case of error
        $con->rollback();
        echo "<p>Error removing order: " . $e->getMessage() . "</p>";
    }
}

//handle refresh price request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['refresh_price'])) {
    $order_id = $_POST['order_id'];
    updateOrderTotal($con, $order_id);
    header("Location: view_orders.php"); // redirect to avoid form resubmission
    exit;
}
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Orders</title>
    <script>
        //automatically set the order date and time based on user's computer time
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
        <h2 class="page-title">Orders</h2>
        <!-- Add Order -->
        <form action="view_orders.php" method="POST" class="adding-form">
            <h3>Add New Order</h3>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <div class="form-columns">
                <div class="left-column">
                    <div class="form-box">
                        <label for="order_date">Order Date:</label>
                        <input type="date" id="order_date" name="order_date" required>
                    </div>
                    <div class="form-box">
                        <label for="order_time">Order Time:</label>
                        <input type="time" id="order_time" name="order_time" required>
                    </div>
                </div>
                <div class="right-column">
                    <div class="form-box">
                        <label for="recipient">Recipient:</label>
                        <input type="text" id="recipient" name="recipient_name" required>
                    </div>
                    <div class="form-box">
                        <label for="order_status">Order Status:</label>
                        <select id="order_status" name="order_status" required>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-box">
                        <label for="user_id">User:</label>
                        <select id="user_id" name="user_id" required>
                            <?php
                            if ($users_result && $users_result->num_rows > 0) {
                                while ($user = $users_result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($user['user_id']) . "'>" . htmlspecialchars($user['username']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>No users found</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="save-btn" name="create_order">Add Order</button>
        </form>
    </div>
    <div class="page-container">
        <h2 class="page-title">System's Orders</h2>

        <!-- table to view existing orders -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <!-- table headers with sorting functionality -->
                        <th><a href="?sort_column=order_id&sort_order=<?php echo $next_sort_order; ?>">Order ID</a></th>
                        <th><a href="?sort_column=user_id&sort_order=<?php echo $next_sort_order; ?>">User ID</a></th>
                        <th><a href="?sort_column=username&sort_order=<?php echo $next_sort_order; ?>">Username</a></th>
                        <th><a href="?sort_column=order_date&sort_order=<?php echo $next_sort_order; ?>">Order Date</a></th>
                        <th><a href="?sort_column=order_time&sort_order=<?php echo $next_sort_order; ?>">Order Time</a></th>
                        <th><a href="?sort_column=order_status&sort_order=<?php echo $next_sort_order; ?>">Status</a></th>
                        <th><a href="?sort_column=recipient_name&sort_order=<?php echo $next_sort_order; ?>">Recipient</a></th>
                        <th>Total Cost</th>
                        <th>Reset Price</th>
                        <th>Manage Order Details</th>
                        <th>View Order Content</th>
                        <th>View Invoice</th>
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
                            echo "<td>" . htmlspecialchars($row['recipient_name'] ?? "N/A") . "</td>";
                            echo "<td>£" . $total_cost . "</td>";
                            // refresh price button
                            echo "<td>
                                    <form method='POST'>
                                        <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                                        <button type='submit' name='refresh_price' class='refresh-btn'>Refresh</button>
                                    </form>
                                  </td>";
                            //manage order details
                            echo "<td><a href='manage_orders.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='manage-btn'>Manage Order Details</a></td>";
                            //view order content
                            echo "<td><a href='view_order_content.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='manage-btn'>View Order Content</a></td>";
                            //view invoice
                            echo "<td><a href='view_invoice.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='manage-btn'>View Invoice</a></td>";
                            // remove order button
                            echo "<td>
                                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this order?\");'>
                                        <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                                        <button type='submit' name='remove_order' class='remove-btn'>Remove Order</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
        </div>
    </div>
</body>
</html>