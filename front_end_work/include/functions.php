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
function checkAdmin() {
  if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
      echo "<script>
              alert('You are not authorized to access this page.');
              window.location.href = 'home.php';
            </script>";
      exit;
  }
}

// Function to update the total cost in the orders table
function updateOrderTotal($con, $order_id) {
  // Recalculate the total order price
  $total_order_price = 0;
  $sql = "SELECT order_quantity, unit_price FROM order_items 
          LEFT JOIN parts ON order_items.part_id = parts.part_id 
          WHERE order_items.order_id = ?";
  $stmt = $con->prepare($sql);
  $stmt->bind_param('i', $order_id);
  $stmt->execute();
  $result = $stmt->get_result();

  // Sum up the total price of all order items
  while ($row = $result->fetch_assoc()) {
      $total_order_price += $row['order_quantity'] * $row['unit_price'];
  }

  // Update the total_cost in the orders table
  $update_sql = "UPDATE orders SET total_cost = ? WHERE order_id = ?";
  $update_stmt = $con->prepare($update_sql);
  $update_stmt->bind_param('di', $total_order_price, $order_id);
  $update_stmt->execute();
}
//Function to log actions
function logAction($user_id, $user_name, $action_type, $desc) {
  global $con; // Access the database connection

  //Prepare the SQL query to insert log data
  $sql = "INSERT INTO system_logs (user_id, user_name, action_type, log_description) 
          VALUES (?, ?, ?, ?)";
  $stmt = $con->prepare($sql);
  
  //Bind the parameters to the prepared statement
  $stmt->bind_param('isss', $user_id, $user_name, $action_type, $desc);

  //Execute the query
  if ($stmt->execute()) {
      return true;  //Successfully logged the action
  } else {
      return false; //Failed to log the action
  }
}

?>
