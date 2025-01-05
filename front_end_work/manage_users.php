<?php
include('include/init.php'); // Initialise, this includes the database connection
checkAdmin();

// Getting user id from the URL, defaults to 0 to ensure it doesn't break
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

// Fetching user details from the database
$sql = "SELECT users.*, user_details.* FROM users
        LEFT JOIN user_details ON users.user_id = user_details.user_id
        WHERE users.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $result->free();  // Free the result to prevent "Commands out of sync" error
} else {
    die("User not found.");
}

// Handling the form submission when the "Save Changes" button is pressed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    // Update session role
    if ($_SESSION['user_id'] == $user_id) {
        $_SESSION['user_role'] = $user_data['role'];
    }
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the new username already exists in the db
    $check_username_sql = "SELECT COUNT(*) FROM users WHERE username = ? AND user_id != ?";
    $stmt_username = $con->prepare($check_username_sql);
    $stmt_username->bind_param('si', $username, $user_id);
    $stmt_username->execute();
    $stmt_username->bind_result($username_exists);
    $stmt_username->fetch();
    $stmt_username->free_result(); // Free the result

    // Check if the new email already exists in the db
    $check_email_sql = "SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?";
    $stmt_email = $con->prepare($check_email_sql);
    $stmt_email->bind_param('si', $email, $user_id);
    $stmt_email->execute();
    $stmt_email->bind_result($email_exists);
    $stmt_email->fetch();
    $stmt_email->free_result(); // Free the result

    // Check if the new phone number exists in the db
    $check_phone_sql = "SELECT COUNT(*) FROM user_details WHERE phone_number = ? AND user_id != ?";
    $stmt_phone = $con->prepare($check_phone_sql);
    $stmt_phone->bind_param('si', $phone_number, $user_id);
    $stmt_phone->execute();
    $stmt_phone->bind_result($phone_exists);
    $stmt_phone->fetch();
    $stmt_phone->free_result();  // Free the result

    // If any exist then it returns this error
    if ($username_exists > 0) {
        die("Username already exists.");
    } elseif ($email_exists > 0) {
        die("Email already exists.");
    } elseif ($phone_exists > 0) {
        die("Phone number already exists.");
    }

    // If they provide a new password this is where we check that they match
    if (!empty($new_password) && $new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_password_sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $update_password_stmt = $con->prepare($update_password_sql);
        $update_password_stmt->bind_param('si', $hashed_password, $user_id);
        $update_password_stmt->execute();
    } elseif (!empty($new_password)) {
        die("Passwords do not match.");
    }

    // Updating the users table with the new data
    $update_sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param('sssi', $username, $email, $role, $user_id);
    $update_stmt->execute();

    // Updating the user details table with this new data
    $update_details_sql = "UPDATE user_details SET first_name = ?, last_name = ?, phone_number = ? WHERE user_id = ?";
    $update_details_stmt = $con->prepare($update_details_sql);
    $update_details_stmt->bind_param('sssi', $first_name, $last_name, $phone_number, $user_id);
    $update_details_stmt->execute();

    logAction($user_data['user_id'], $user_data['username'], 'UPDATE', 'Updated a user'); // Log the action here
    // Once the action is completed, redirect to view users page
    header('Location: view_users.php');
    exit;
}
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    <div class="page-container">
        <h2 class="page-title">Edit User Details</h2>
        <!-- Container that contains all of the boxes -->
        <div class="form-container">
            <!-- Back Button -->
            <div class="back-button-container">
                <a href="view_users.php" class="back-btn">Back</a>
            </div>
            <form action="manage_users.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" method="POST" class="form">
                <div class="form-columns">
                    <div class="left-column">
                        <div class="form-box">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="role">Role:</label>
                            <select id="role" name="role" required>
                                <option value="Admin" <?php echo ($user['role'] === 'Admin' ? 'selected' : ''); ?>>Admin</option>
                                <option value="User" <?php echo ($user['role'] === 'User' ? 'selected' : ''); ?>>User</option>
                            </select>
                        </div>

                        <div class="form-box">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                    </div>
                    <div class="right-column">
                        <div class="form-box">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>

                        <div class="form-box">
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                        </div>

                        <!-- Changing password section -->
                        <div class="form-box">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        <div class="form-box">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                </div>
                <button type="submit" class="save-btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>