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
// // Fetch sales data from the database
// $sql = "SELECT part_name, quantity_in_stock FROM parts";
// $result = $con->query($sql);

// $product = [];
// $stocks = [];

// if ($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         $Products[] = $row['Products'];
//         $Stocks[] = $row['Stocks'];
//     }
// }

// // Return data as JSON
// echo json_encode([
//     'products' => $product,
//     'stocks' => $stocks
// ]);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get user input from the form
  $part_name = $_POST['part_name'];
  $genre = $_POST['genre'];
  $manufacturer = $_POST['manufacturer'];
  $supplier_id = $_POST['supplier_id'];
  $unit_price = $_POST['unit_price'];
  $quantity_in_stock = $_POST['quantity_in_stock'];
  $reorder_level = $_POST['reorder_level'];
  $description = $_POST['description'];

  // Prepare the SQL query with placeholders
  $stmt = $conn->prepare("INSERT INTO parts (part_name, genre, manufacturer, supplier_id, unit_price, quantity_in_stock, reorder_level, description) VALUES (?, ?)");
  
  // Bind the input parameters to the placeholders
  $stmt->bind_param("ss", $part_name, $genre, $manufacturer, $supplier_id, $unit_price, $quantity_in_stock, $reorder_level, $description); // "ss" means two strings
  
  // Execute the query
  if ($stmt->execute()) {
      echo "New part added successfully!";
  } else {
      echo "Error: " . $stmt->error;
  }

  // Close the statement and connection
  $stmt->close();
  $conn->close();
}

?>