<?php
session_start();
include("include/connection.php");
include("include/functions.php"); // Ensure this is included to access the logAction function

if (isset($_POST['username'], $_POST['password'])) { // check if username and password are entered
    $entered_username = $_POST['username']; // saving inputted data as variables
    $entered_password = $_POST['password'];

    if (($entered_username) && ($entered_password)) { // check if data has been entered
        // Modify the query to check if the user is active
        $login_query = "SELECT * FROM users WHERE username = ? AND active = 1"; // check if the user is active
        $stmt = mysqli_prepare($con, $login_query); // prepare the statement
        mysqli_stmt_bind_param($stmt, 's', $entered_username); // bind the username to the prepared statement
        mysqli_stmt_execute($stmt); // execute the query
        $login_result = mysqli_stmt_get_result($stmt); // fetch the result

        if ($login_result && mysqli_num_rows($login_result) === 1) { // check if user exists
            $user_data = mysqli_fetch_assoc($login_result); // fetch the user data
            // verify the entered password with the hashed password in the DB
            if (password_verify($entered_password, $user_data['password'])) { // check if the password matches
                // if password is correct, create session and redirect
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['user_role'] = $user_data['role'];
                
                // Log the login action (log the user's login event)
                logAction($user_data['user_id'], $user_data['username'], 'LOGIN', 'Logged in at this time'); // Log the action here
                
                header("Location: home.php"); // redirect to home page
                exit;
            } else {
                echo "<script>alert('Wrong password');</script>"; // if password doesn't match
            }
        } else {
            echo "<script>alert('User not found or account is inactive');</script>"; // if no active user is found
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/signin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
</head>
<body>      
<div class="page-container"> 
    <div class="form-container">
        <form action="signin.php" method="post">
            <h3>Sign In</h3>
            <input type="text" placeholder="Enter Username" name="username" required>
            <input type="password" placeholder="Enter Password" name="password" required>
            <input type="submit" name="submit" value="Login" class="form-btn">
        </form>
    </div>
</div>
<?php include('include/footer.php')?>
</body>
</html>
