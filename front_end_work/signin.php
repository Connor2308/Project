<?php
session_start();
include("include/connection.php");
include("include/functions.php");

if (isset($_POST['username'], $_POST['password'])) { 
    $entered_username = $_POST['username']; 
    $entered_password = $_POST['password'];

    if (($entered_username) && ($entered_password)) {
        $login_query = "SELECT * FROM users WHERE username = ? AND active = 1"; 
        $stmt = mysqli_prepare($con, $login_query); 
        mysqli_stmt_bind_param($stmt, 's', $entered_username); 
        mysqli_stmt_execute($stmt); 
        $login_result = mysqli_stmt_get_result($stmt); 

        if ($login_result && mysqli_num_rows($login_result) === 1) { 
            $user_data = mysqli_fetch_assoc($login_result); 
            if (password_verify($entered_password, $user_data['password'])) { 
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['user_role'] = $user_data['role'];
                
                logAction($user_data['user_id'], $user_data['username'], 'LOGIN', 'Logged in at this time'); 
                
                header("Location: home.php"); 
                exit;
            } else {
                $_SESSION['error_message'] = 'Incorrect password or username'; 
                header("Location: signin.php"); 
                exit;
            }
        } else {
            $_SESSION['error_message'] = 'User not found, please try again'; 
            header("Location: signin.php"); 
            exit;
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
    <script src="error_popup.js" defer></script>
</head>
<body>
<div class="page-container"> 
    <div class="form-container">
        <form action="signin.php" method="post">
            <h3>Sign In</h3>
            
            <!-- Error Banner -->
            <div id="errorBanner">
                <span id="errorText"></span>
            </div>
            
            <input type="text" placeholder="Enter Username" name="username" required>
            <input type="password" placeholder="Enter Password" name="password" required>
            <input type="submit" name="submit" value="Login" class="form-btn">
        </form>
    </div>
</div>

<?php include('include/footer.php') ?>

<?php
if (isset($_SESSION['error_message'])) {
    echo "<script>window.onload = function() { showErrorMessage('" . $_SESSION['error_message'] . "'); }</script>";
    unset($_SESSION['error_message']); 
}
?>

</body>
</html>