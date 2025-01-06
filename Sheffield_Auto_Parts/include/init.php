<?php
session_start(); //use this to start the session and to stop me having to write it out every time
include('include/connection.php');
include('include/functions.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
$user_data = check_login($con);
$user_id = $user_data['user_id'];

// $connection = mysql_connect("localhost", "root", "", "");
// mysql_select_db("parts")or die(mysql_error());
// $Parts = mysql_real_escape_string($_GET['parts_name']);
// $Results = mysql_query("SELECT * FROM parts WHERE parts_name = '" + $Parts + "'");
// print_r($Results);
?>
