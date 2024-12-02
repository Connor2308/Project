<?php
//the basis for my superglobals and how i submit my forms comes from this documentation https://www.w3schools.com/php/php_superglobals_post.asp
session_start();
include("include/connection.php");
include("include/functions.php");

if(isset($_POST['username'], $_POST['password'])){ //whatever user has entered in box
  $entered_username = $_POST['username']; //saving inputted data as variables, this is used all over the codebase
  $entered_password = $_POST['password'];

  if(($entered_username) && ($entered_password)){ //data has been entered into these variables if it is true or not empty
    $login_query = "SELECT * FROM users WHERE username = ?"; //login query
    $stmt = mysqli_prepare($con, $login_query); //mysqli_prepare prepares the statement for execution, the ? is just a temp thing and will be changed with the username, this prevents sql injections
    mysqli_stmt_bind_param($stmt, 's', $entered_username); //this bind the entered username to the prepared statement, the 's' specifies that the parameter is a string, as usernames are strings
    mysqli_stmt_execute($stmt); //runs the query
    $login_result = mysqli_stmt_get_result($stmt);//fetching the query and then login result is used to see if it exists (username)
    
    if($login_result && mysqli_num_rows($login_result) === 1){ //checking if the user name exists
      $user_data = mysqli_fetch_assoc($login_result); //fetching the user data
      //verify the entered password with the hashed password in the DB
      if(password_verify($entered_password, $user_data['password'])){ //this is where we acutally 'dehash' the password with password_verify()
        //if the password matches!, create session and redirect
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['user_role'] = $user_data['role'];
        header("Location: home.php"); //redirect to home page
        exit;
      }
    }
    //js alert as they have done something wrong
    echo "<script>alert('Wrong username or password');</script>";//js alert informing user of wrong user or pass
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