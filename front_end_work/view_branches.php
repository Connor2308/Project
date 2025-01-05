<?php
include('include/init.php');

// Handle deactivating a branch
if (isset($_GET['deactivate_branch_id'])) {
    $deactivate_branch_id = intval($_GET['deactivate_branch_id']);
    
    // Fetch branch details before deletion
    $fetch_sql = "SELECT branch_name FROM branches WHERE branch_id = ?";
    $stmt = $con->prepare($fetch_sql);
    $stmt->bind_param('i', $deactivate_branch_id);
    $stmt->execute();
    $stmt->bind_result($branch_name);
    $stmt->fetch();
    $stmt->close();

    // Delete the branch
    $deactivate_sql = "DELETE FROM branches WHERE branch_id = ?";
    $stmt = $con->prepare($deactivate_sql);
    $stmt->bind_param('i', $deactivate_branch_id);
    if ($stmt->execute()) {
        logAction($user_data['user_id'], $user_data['username'], 'DELETE',  ); // Log the action here
        header('Location: view_branches.php'); // Redirect to the branches page after successful deletion
        exit;
    } else {
        $error_message = "Error deleting branch. Please try again.";
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
    $insert_sql = "INSERT INTO branches (branch_name, branch_address, branch_phone, branch_email) VALUES (?, ?, ?, ?)";
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
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['branch_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['branch_address']); ?></td>
                                <td><?php echo htmlspecialchars($row['branch_phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['branch_email']); ?></td>
                                <td><a href="manage_branches.php?branch_id=<?php echo $row['branch_id']; ?>" class="manage-btn">Manage Branch</a></td>
                                <td><a href="view_branches.php?deactivate_branch_id=<?php echo $row['branch_id']; ?>" class="remove-btn" onclick="return confirm('Are you sure you want to deactivate this branch?');">Deactivate Branch</a></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No branches found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include('include/footer.php')?>
</body>
</html>