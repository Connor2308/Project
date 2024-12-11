<?php
include('include/init.php');
checkAdmin();

//sorting section, when you click the table headers
$sort_column = $_GET['sort_column'] ?? 'user_id';
$sort_order = $_GET['sort_order'] ?? 'ASC';
$next_sort_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC'; 

//SQL Query to fetch active user data
$sql = "SELECT 
            users.user_id,
            users.username,
            users.password,
            users.email,
            users.role,
            user_details.first_name,
            user_details.last_name,
            user_details.phone_number
        FROM 
            users
        LEFT JOIN 
            user_details 
        ON 
            users.user_id = user_details.user_id
        WHERE 
            users.active = 1
        ORDER BY 
            $sort_column $sort_order";

$result = $con->query($sql);

$error_message = ''; //creating the error message varibale for later just incase

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    //capture form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];

    //check if username already exists
    $check_username_sql = "SELECT user_id FROM users WHERE username = ?";
    $stmt = $con->prepare($check_username_sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $username_result = $stmt->get_result();

    //check if email already exists
    $check_email_sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $con->prepare($check_email_sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $email_result = $stmt->get_result();

    if ($username_result->num_rows > 0) {
        // username already exists
        $error_message = "Username already exists. Please choose a different username.";
    } elseif ($email_result->num_rows > 0) {
        //email already exists
        $error_message = "Email already exists. Please choose a different email address.";
    } else {
        //proceed with user creation if no duplicates found
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); //hashing the password

        //inserterting the new user into the database
        $insert_user_sql = "INSERT INTO users (username, password, email, role) 
                            VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($insert_user_sql);
        $stmt->bind_param('ssss', $username, $hashed_password, $email, $role);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id; //get the inserted user's ID, this is for the auto increment bit

            //inserting new details 
            $insert_details_sql = "INSERT INTO user_details (user_id, first_name, last_name, phone_number)
                                   VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($insert_details_sql);
            $stmt->bind_param('isss', $user_id, $first_name, $last_name, $phone_number);
            $stmt->execute();
            logAction($user_data['user_id'], $user_data['username'], 'CREATE', "Created $user_id"); // Log the action here

            header('Location: view_users.php');//if it correct it will redirect to the view_users table with the new user
            exit;
        } else {
            $error_message = "Error creating user. Please try again.";
        }
    }
}

//if the remove user button is pressed
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_user'])) {
    $user_id = $_POST['user_id'];

    //beginning transaction to make sure all the data is consistent
    $con->begin_transaction();

    try {
        // Set the user's status to inactive
        $update_user_sql = "UPDATE users SET active = 0 WHERE user_id = ?";
        $update_user_stmt = $con->prepare($update_user_sql);
        $update_user_stmt->bind_param('i', $user_id);
        if (!$update_user_stmt->execute()) {
            throw new Exception('Error deactivating the user.');
        }

        //commit transaction
        logAction($user_data['user_id'], $user_data['username'], 'DELETE', "Removed $user_id "); // Log the action here
        $con->commit();
        header('Location: view_users.php'); //redirects you to the users page if it is successful
        exit;
    } catch (Exception $e) {
        //roll back just in case anything breaks
        $con->rollback();
        echo "<p>Error removing user: " . $e->getMessage() . "</p>";
    }
}

?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <link rel="stylesheet" href="style/useradd.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Users</title>
</head>
<body>
    <?php include('include/header.php')?>
    <div class="page-container">
        <h2 class="page-title">System's Users</h2>
        <!-- User creation form -->
        <form action="view_users.php" method="POST" class="user-form">
            <h3>Create New User</h3>
            <div class="form-columns">
                <div class="left-column">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>

                <div class="right-column">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number:</label>
                        <input type="text" id="phone_number" name="phone_number">
                    </div>
                </div>
            </div>

            <button type="submit" name="create_user" class="submit-btn">Create User</button>
        </form>

        <!-- User table -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th><a href="?sort_column=user_id&sort_order=<?php echo $next_sort_order; ?>">User ID</a></th>
                        <th><a href="?sort_column=username&sort_order=<?php echo $next_sort_order; ?>">Username</a></th>
                        <th><a href="?sort_column=email&sort_order=<?php echo $next_sort_order; ?>">Email</a></th>
                        <th><a href="?sort_column=role&sort_order=<?php echo $next_sort_order; ?>">Role</a></th>
                        <th><a href="?sort_column=first_name&sort_order=<?php echo $next_sort_order; ?>">First Name</a></th>
                        <th><a href="?sort_column=last_name&sort_order=<?php echo $next_sort_order; ?>">Last Name</a></th>
                        <th><a href="?sort_column=phone_number&sort_order=<?php echo $next_sort_order; ?>">Phone Number</a></th>
                        <th>Manage Users</th>
                        <th>Delete Users</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['user_id']}'>";
                            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['first_name'] ?? "N/A") . "</td>";
                            echo "<td>" . htmlspecialchars($row['last_name'] ?? "N/A") . "</td>";
                            echo "<td>" . htmlspecialchars($row['phone_number'] ?? "N/A") . "</td>";
                            echo "<td><a href='manage_users.php?user_id=" . htmlspecialchars($row['user_id']) . "' class='manage-btn'>Manage User</a></td>";
                            
                            // Remove User button
                            echo "<td>
                                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this user?.\");'>
                                        <input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'>
                                        <button type='submit' name='remove_user' class='remove-btn'>Remove User</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No users found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
