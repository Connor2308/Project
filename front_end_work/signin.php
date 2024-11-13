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
         <input type="email" name="email" required placeholder="Email">
         <input type="password" name="password" required placeholder="Password">
         <input type="submit" name="submit" value="Sign In" class="form-btn">
         <p>Already have an account? <a href="signup.php">Sign up</a></p>
      </form>
    </div>


    </div>

<?php include('include/footer.php')?>
</body>
</html>