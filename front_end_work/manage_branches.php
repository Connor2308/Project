<?php
include('include/init.php'); // Initialise, this includes the database connection
// Verifying admin?
checkAdmin();

// Getting branch id from the URL, defaults to 0 to ensure it doesn't break
$branch_id = isset($_GET['branch_id']) ? $_GET['branch_id'] : 0;

// Fetching branch details from the database
$sql = "SELECT * FROM branches WHERE branch_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $branch_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $branch = $result->fetch_assoc();
    $result->free();  // Free the result to prevent "Commands out of sync" error
} else {
    die("Branch not found.");
}

// Handling the form submission when the "Save Changes" button is pressed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch_name = $_POST['branch_name'];
    $branch_address = $_POST['branch_address'];
    $branch_phone = $_POST['branch_phone'];
    $branch_email = $_POST['branch_email'];

    // Check if the branch name already exists
    $check_branch_sql = "SELECT COUNT(*) FROM branches WHERE branch_name = ? AND branch_id != ?";
    $stmt_branch = $con->prepare($check_branch_sql);
    $stmt_branch->bind_param('si', $branch_name, $branch_id);
    $stmt_branch->execute();
    $stmt_branch->bind_result($branch_exists);
    $stmt_branch->fetch();
    $stmt_branch->free_result(); // Free the result

    if ($branch_exists > 0) {
        die("Branch name already exists.");
    }

    // Updating the branch's information
    $update_sql = "UPDATE branches SET branch_name = ?, branch_address = ?, branch_phone = ?, branch_email = ? WHERE branch_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param('ssssi', $branch_name, $branch_address, $branch_phone, $branch_email, $branch_id);
    $update_stmt->execute();

    logAction($user_data['user_id'], $user_data['username'], 'UPDATE', 'Updated branch: ' . $branch_name); // Log the action here
    // Once the action is completed, redirect to the branches list page
    header('Location: view_branches.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/supplier.css">
    <link rel="stylesheet" href="style/base.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Branch</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <h2 class="page-title">Edit Branch Details</h2>
    <!-- Container that contains all of the boxes -->
    <div class="page-container">
        <form action="manage_branches.php?branch_id=<?php echo htmlspecialchars($branch['branch_id']); ?>" method="POST" class="manage-supplier-form">
            <div class="form-box">
                <label for="branch_name">Branch Name:</label>
                <input type="text" id="branch_name" name="branch_name" value="<?php echo htmlspecialchars($branch['branch_name']); ?>" required>
            </div>

            <div class="form-box">
                <label for="branch_phone">Contact Phone:</label>
                <input type="text" id="branch_phone" name="branch_phone" value="<?php echo htmlspecialchars($branch['branch_phone']); ?>" required>
            </div>

            <div class="form-box">
                <label for="branch_email">Contact Email:</label>
                <input type="email" id="branch_email" name="branch_email" value="<?php echo htmlspecialchars($branch['branch_email']); ?>" required>
            </div>

            <div class="form-box">
                <label for="branch_address">Address:</label>
                <textarea id="branch_address" name="branch_address" required><?php echo htmlspecialchars($branch['branch_address']); ?></textarea>
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>