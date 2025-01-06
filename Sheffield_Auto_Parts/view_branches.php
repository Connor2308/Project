<?php
include('include/init.php');

// Handle deactivating a branch
if (isset($_GET['deactivate_branch_id'])) {
    $deactivate_branch_id = intval($_GET['deactivate_branch_id']);
    
    // Fetch branch details before deactivation
    $fetch_sql = "SELECT branch_name FROM branches WHERE branch_id = ?";
    $stmt = $con->prepare($fetch_sql);
    $stmt->bind_param('i', $deactivate_branch_id);
    $stmt->execute();
    $stmt->bind_result($branch_name);
    $stmt->fetch();
    $stmt->close();

    // Deactivate the branch
    $deactivate_sql = "UPDATE branches SET active = 0 WHERE branch_id = ?";
    $stmt = $con->prepare($deactivate_sql);
    $stmt->bind_param('i', $deactivate_branch_id);
    if ($stmt->execute()) {
        logAction($user_data['user_id'], $user_data['username'], 'DEACTIVATE', "Deactivated branch: $branch_name"); // Log the action here
        header('Location: view_branches.php'); // Redirect to the branches page after successful deactivation
        exit;
    } else {
        $error_message = "Error deactivating branch. Please try again.";
    }
}

// Handle adding a new branch
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_branch'])) {
    // Capture form data
    $branch_name = $_POST['branch_name'];
    $branch_address = $_POST['branch_address'];
    $branch_phone = $_POST['branch_phone'];
    $branch_email = $_POST['branch_email'];

    // Insert new branch into the database
    $insert_sql = "INSERT INTO branches (branch_name, branch_address, branch_phone, branch_email, active) VALUES (?, ?, ?, ?, 1)";
    $stmt = $con->prepare($insert_sql);
    $stmt->bind_param('ssss', $branch_name, $branch_address, $branch_phone, $branch_email);

    if ($stmt->execute()) {
        logAction($user_data['user_id'], $user_data['username'], 'CREATE', 'Added a branch'); // Log the action here
        header('Location: view_branches.php'); // Redirect to the branches page after successful addition
        exit;
    } else {
        $error_message = "Error adding branch. Please try again.";
    }
}

// Define default sorting variables
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'branch_id';
$sort_order = isset($_GET['sort_order']) && $_GET['sort_order'] === 'desc' ? 'desc' : 'asc';
$next_sort_order = $sort_order === 'asc' ? 'desc' : 'asc';

// Fetch branches from the database
$sql = "SELECT branch_id, branch_name, branch_address, branch_phone, branch_email FROM branches 
        WHERE active = 1
        ORDER BY $sort_column $sort_order";
$result = $con->query($sql);

if (!$result) {
    die("Error fetching branches: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css"> 
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branches</title>
</head>
<body>
<?php include('include/header.php')?>
    <div class="page-container">
        <h2 class="page-title">Company Branches</h2>
        <!-- Add Branch Form -->
        <form action="view_branches.php" method="POST" class="adding-form">
            <h3>Add New Branch</h3>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <div class="form-columns">
                <div class="left-column">
                    <div class="form-box">
                        <label for="branch_name">Branch Name:</label>
                        <input type="text" id="branch_name" name="branch_name" required>
                    </div>
                    <div class="form-box">
                        <label for="branch_phone">Contact Phone:</label>
                        <input type="text" id="branch_phone" name="branch_phone" required>
                    </div>
                </div>
                <div class="right-column">
                    <div class="form-box">
                        <label for="branch_email">Contact Email:</label>
                        <input type="email" id="branch_email" name="branch_email" required>
                    </div>
                    <div class="form-box">
                        <label for="branch_address">Address:</label>
                        <input type="text" id="branch_address" name="branch_address" required>
                    </div>
                </div>
            </div>
            <button type="submit" name="add_branch" class="save-btn">Add Branch</button>
        </form>

        <!-- Branches Table -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th><a href="?sort_column=branch_id&sort_order=<?php echo $next_sort_order; ?>">Branch ID</a></th>
                        <th><a href="?sort_column=branch_name&sort_order=<?php echo $next_sort_order; ?>">Branch Name</a></th>
                        <th><a href="?sort_column=branch_address&sort_order=<?php echo $next_sort_order; ?>">Branch Address</a></th>
                        <th><a href="?sort_column=branch_phone&sort_order=<?php echo $next_sort_order; ?>">Branch Phone</a></th>
                        <th><a href="?sort_column=branch_email&sort_order=<?php echo $next_sort_order; ?>">Branch Email</a></th>
                        <th>Manage</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fill table rows with data from the query
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['branch_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['branch_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['branch_address']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['branch_phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['branch_email']) . "</td>";
                            echo "<td><a href='manage_branches.php?branch_id=" . htmlspecialchars($row['branch_id']) . "' class='manage-btn'>Manage Branch</a></td>";
                            echo "<td><a href='view_branches.php?deactivate_branch_id=" . htmlspecialchars($row['branch_id']) . "' class='remove-btn' onclick='return confirm(\"Are you sure you want to delete this branch?\");'>Delete Branch</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No branches found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>