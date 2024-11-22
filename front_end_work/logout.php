<?php
session_start();//https://www.php.net/manual/en/function.session-start.php, this is needed to keep my user logged in and so only they can access these pages. this is used by check login fucntion in functions 
//check to see if it is set to something(it is as they cant log out if they are not logged in)
if(isset($_SESSION['user_id'])){
  unset($_SESSION['user_id']); //if they log out then we dont want them to be the logged in user anymore
}
header("Location: signin.php");//redirect
?>