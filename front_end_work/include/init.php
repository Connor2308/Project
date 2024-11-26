<?php
session_start();
include('include/connection.php');
include('include/functions.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}
$user_data = check_login($con);
$user_id = $user_data['user_id'];
?>
