<?php
session_start();//https://www.php.net/manual/en/function.session-start.php, this is needed to keep my user logged in and so only they can access these pages. this is used by check login fucntion in functions 
include("includes/connection.php");
include("includes/functions.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Creation Page</title>
</head>
<body>
<?php include('include/header.php')?>



<?php include('include/footer.php')?>
</body>
</html>