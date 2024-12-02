<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "sheffield_auto_spares_db";

$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
 //if there is an error in connection this will alert us
if ($con->connect_error){
  die("Connection failed: " . $con->connect_error);
}
// Fetch sales data from the database
$sql = "SELECT part_name, quantity_in_stock FROM parts";
$result = $con->query($sql);

$product = [];
$stocks = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $Products[] = $row['Products'];
        $Stocks[] = $row['Stocks'];
    }
}

// Return data as JSON
echo json_encode([
    'products' => $product,
    'stocks' => $stocks
]);

?>