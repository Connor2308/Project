<?php
//this is called in js/update_stock.js as an ajax request
include('include/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_id = intval($_POST['part_id']); //getting the part id from the post request
    $action = $_POST['action']; //getting the type of action either minus or plus

    $result = $con->query("SELECT quantity_in_stock FROM parts WHERE part_id = $part_id"); //fetching the current quantity
    if ($result->num_rows > 0) { //just making sure the given id is actually associated with the data base and is a part
        $row = $result->fetch_assoc();
        $quantity = $row['quantity_in_stock']; 

        //update quantity based on action
        if ($action === 'increase') {
            $quantity++;
        } elseif ($action === 'decrease' && $quantity > 0) {
            $quantity--;
        }

        //save updated amount to db
        $con->query("UPDATE parts SET quantity_in_stock = $quantity WHERE part_id = $part_id");

        //returning the updated quantit to js file
        echo $quantity;
    }
}
?>
