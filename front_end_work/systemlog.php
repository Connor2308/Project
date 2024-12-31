<?php
include('include/init.php'); //Initialise the system
checkAdmin();
// Get the selected user filter from the dropdown (if any)
$user_filter = isset($_POST['user_filter']) ? $_POST['user_filter'] : '';

// SQL query to fetch log actions,
$sql = "SELECT system_logs.log_id, system_logs.user_id, system_logs.user_name, system_logs.action_type, system_logs.log_description, system_logs.log_timestamp 
        FROM system_logs 
        WHERE 1"; // 1 is a placeholder for the WHERE clause; we add conditions later

if ($user_filter) {
    $sql .= " AND system_logs.user_id = $user_filter"; //filter by selected user ID
}

$sql .= " ORDER BY system_logs.log_timestamp DESC"; //order logs by timestamp

$result = $con->query($sql);

// Get the list of users for the dropdown filter
$user_sql = "SELECT user_id, username FROM users ORDER BY username";
$user_result = $con->query($user_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs</title>
</head>
<body>
    <?php include('include/header.php'); ?>

    <div class="page-container">
        <h2 class="page-title">System Logs</h2>

<!-- Filter Form -->
    <form method="POST" class="filter-form">
        <div class="filter-group">
            <label for="user_filter">Filter by User:</label>
            <select name="user_filter" id="user_filter">
        <option value="">All Users</option>
        <?php
        while ($user_row = $user_result->fetch_assoc()) {
            $selected = ($user_row['user_id'] == $user_filter) ? 'selected' : '';
            echo "<option value='" . $user_row['user_id'] . "' $selected>#" . $user_row['user_id'] . " - " . htmlspecialchars($user_row['username']) . "</option>";
        }
        ?>
        </select>
    </div>
        <button type="submit" class="filter-btn">Apply Filter</button>
    </form>

        <!-- Logs Table -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Action</th>
                        <th>Notes</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display logs
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['action_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['log_description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['log_timestamp']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No logs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('include/footer.php'); ?>
</body>
</html>
