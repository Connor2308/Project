<?php
//checking the user who is logged in function
function check_login($con){
  if(isset($_SESSION['user_id'])){//the current loggin in user in the session
    $users_id = $_SESSION['user_id'];
    $check_login_query = "SELECT * FROM users WHERE user_id = '$users_id'"; //will only be 1 user thanks to unique user ids
    $logged_in_result = mysqli_query($con,$check_login_query);
    if($logged_in_result && mysqli_num_rows($logged_in_result) === 1){
      $user_data = mysqli_fetch_assoc($logged_in_result);//fetches a result as am array 
      return $user_data;//the loggin users data
    }
  }
  //redirect to login page as login has failed
  header("Location: signin.php");
}
?>