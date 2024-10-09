<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "to_do_list";
//this establishes a connection to the database, conn will be our database connection variable
$conn = new mysqli($servername, $username, $password, $dbname);

//if there is an error in connection this will alert us
if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

$conn = mysqli_connect('localhost','root','','user_db');

?>