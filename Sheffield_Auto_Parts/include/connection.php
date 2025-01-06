<?php
//database details
$dbhost = "localhost"; 
$dbuser = "root";
$dbpass = "";
$dbname = "sheffield_auto_spares_db";

$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
 //if there is an error in connection this will alert us
if ($con->connect_error){
  die("Connection failed: " . $con->connect_error);
}
?>