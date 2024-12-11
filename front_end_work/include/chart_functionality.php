<?php
// Fetch sales data from the database
$sql = "SELECT part_name, quantity_in_stock FROM parts";
$result = $con->query($sql);

$product = [];
$stocks = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row['part_name'];
        $stocks[] = $row['quantity_in_stock'];
    }
}

// Return data as JSON
echo json_encode([
    'part_name' => $product,
    'quantity_in_stock' => $stocks
]);
?>