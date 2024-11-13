<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">

    <link rel="stylesheet" href="style/signin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>

        
    <?php include('include/header.php')?>
    <div class="page-container"> 
        
    
    
    <div class="form-container">
        <form action="signup.php" method="post">
         <h3>Sign Up</h3>
         <input type="email" name="email" required placeholder="Email">
         <input type="password" name="password" required placeholder="Password">
         <input type="submit" name="submit" value="Login" class="form-btn">
         <p>or <a href="signin.php">Sign in</a></p>
      </form>
    </div>


    </div>

<?php include('include/footer.php')?>
</body>
</html>