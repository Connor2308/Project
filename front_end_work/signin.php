<?php
//the basis for my superglobals and how i submit my forms comes from this documentation https://www.w3schools.com/php/php_superglobals_post.asp
session_start();
include("include/connection.php");
include("include/functions.php");

if(isset($_POST['username'], $_POST['password'])){ //whatever user has entered in box
  $entered_username = $_POST['username']; //saving inputted data as variables, this is used all over the codebase
  $entered_password = $_POST['password'];

  if(($entered_username) && ($entered_password)){ //data has been entered into these variables if it is true
    $login_query = "SELECT * FROM users WHERE username = '$entered_username'";
    $login_result = mysqli_query($con, $login_query);
    
    if($login_result){//if it returns a username from the db
      if($login_result && mysqli_num_rows($login_result) === 1){ //only one row should return since usernams are unique, we did this in account creation
        $user_data = mysqli_fetch_assoc($login_result);//fetching all the associated data to the login result or the username
        //if passwords match then we set the current logged in user in the session to the user
        if($user_data['password'] === $entered_password){ 
          $_SESSION['user_id'] = $user_data['user_id']; 
          header("Location: home.php");//redirect to home page
        }
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
<?php include('include/header.php')?>
<div class="page-container"> 
    <div class="form-container">
        <form action="signin.php" method="post">
            <h3>Sign In</h3>
            <input type="text" placeholder="Enter Username" name="username" required>
            <input type="password" placeholder="Enter Password" name="password" required>
            <input type="submit" name="submit" value="Login" class="form-btn">
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </form>
    </div>
</div>
<?php include('include/footer.php')?>
</body>
</html>