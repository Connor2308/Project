<?php
include('include/init.php'); //initialise everything like user data

//Sorting Section
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'part_id'; //if nothing is in the sort column eg when we load up the page we will sort in the part_id
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC'; //this ensures that when the page is first loaded it ensures that it is set to ASC order 
$next_sort_order = ($sort_order == 'ASC') ? 'DESC' : 'ASC'; // this bit is for toggling between ASC and DESC order

//Handling filters
$genre_filter = isset($_POST['genre']) ? $_POST['genre'] : [];
$min_price = isset($_POST['min_price']) && is_numeric($_POST['min_price']) ? $_POST['min_price'] : null;
$max_price = isset($_POST['max_price']) && is_numeric($_POST['max_price']) ? $_POST['max_price'] : null;

//sql selection to populate the table, including a join for the suppliers id
$sql = "SELECT 
            parts.part_id, 
            parts.part_name, 
            parts.description, 
            parts.genre, 
            parts.manufacturer, 
            parts.unit_price, 
            parts.quantity_in_stock, 
            parts.reorder_level, 
            suppliers.supplier_name 
        FROM parts 
        INNER JOIN suppliers ON parts.supplier_id = suppliers.supplier_id";

//adding filters to my query to only fetch what we want
$conditions = []; //array that we store any conditions in eg filters

if ($min_price !== null) {
    $conditions[] = "parts.unit_price >= $min_price";
}
if ($max_price !== null) {
    $conditions[] = "parts.unit_price <= $max_price";
}
if (!empty($genre_filter)) {
    $genre_list = implode("','", array_map('addslashes', $genre_filter));
    $conditions[] = "parts.genre IN ('$genre_list')";//only get specific 'genres' of parts that the user want, ik genre is not the right word but its in now :)
}

//if the conditions array is NOT empty we IMPLODE ("Join array elements with a string") this to the previously written sql statement
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

//adding the ORDER BY clause (this is always added, ust kept breaking when everywhere else but here
$sql .= " ORDER BY $sort_column $sort_order";
//finally we execute the query :)
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parts Inventory</title>
</head>
<body>
    <?php include('include/header.php'); ?>
    
    <div class="page-container">
        <h2 class="page-title">Car Parts Inventory</h2>
        
        <!-- Filter Form (This is split into 3 sections, filters left which is price filtering, filters right which is genre check boxes and a search box, also a search button  to execute the filters) -->
        <form id="filter-form" method="POST">
            <!-- price filter section -->
            <div class="filters-left">
                <label for="min_price">Min Price:</label>
                <input type="number" id="min_price" name="min_price" min="0" step="0.01" value="<?php echo isset($_POST['min_price']) ? htmlspecialchars($_POST['min_price']) : ''; ?>">

                <label for="max_price">Max Price:</label>
                <input type="number" id="max_price" name="max_price" min="0" step="0.01" value="<?php echo isset($_POST['max_price']) ? htmlspecialchars($_POST['max_price']) : ''; ?>">
            </div>
            <!-- genre box, if there are more add them here just follow the format of the others -->
            <div class="filters-right">
                <label>Filter by Genre:</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="engine" name="genre[]" value="Engine" <?php echo in_array('Engine', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="engine">Engine</label>

                    <input type="checkbox" id="exhaust" name="genre[]" value="Exhaust" <?php echo in_array('Exhaust', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="exhaust">Exhaust</label>

                    <input type="checkbox" id="body" name="genre[]" value="Body" <?php echo in_array('Body', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="body">Body</label>

                    <input type="checkbox" id="brakes" name="genre[]" value="Brakes" <?php echo in_array('Brakes', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="brakes">Brakes</label>

                    <input type="checkbox" id="transmission" name="genre[]" value="Transmission" <?php echo in_array('Transmission', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="transmission">Transmission</label>
                </div>
            </div>
            <!-- a search box section -->
            <div class="filters-search">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" placeholder="Search parts..." value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
            </div>
            <!-- apply filter button/ this is disabled by default but is enabled by js when the user actually selects a filter to apply, this is cause it kept breaking -->
            <button type="submit" id="apply-filters" disabled>Apply Filters</button> 
        </form>

        <!-- table bit -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <!-- here are the table headers with the the next sort order button in there aswell so if they click it then the order will change -->
                        <th><a href="?sort_column=part_id&sort_order=<?php echo $next_sort_order; ?>">Part ID</a></th>
                        <th><a href="?sort_column=part_name&sort_order=<?php echo $next_sort_order; ?>">Part Name</a></th>
                        <th><a href="?sort_column=description&sort_order=<?php echo $next_sort_order; ?>">Description</a></th>
                        <th><a href="?sort_column=genre&sort_order=<?php echo $next_sort_order; ?>">Type</a></th>
                        <th><a href="?sort_column=manufacturer&sort_order=<?php echo $next_sort_order; ?>">Manufacturer</a></th>
                        <th><a href="?sort_column=unit_price&sort_order=<?php echo $next_sort_order; ?>">Unit Price</a></th>
                        <th><a href="?sort_column=supplier_name&sort_order=<?php echo $next_sort_order; ?>">Supplier</a></th>
                        <th><a href="?sort_column=reorder_level&sort_order=<?php echo $next_sort_order; ?>">Reorder Level</a></th>
                        <th><a href="?sort_column=quantity_in_stock&sort_order=<?php echo $next_sort_order; ?>">Quantity in Stock</a></th>
                        <th>Update Stock Levels</th>
                        <th>Manage Part Details</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- actually fill the table with each row of data the sql fetches from the db -->
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['part_id']}'>";
                            echo "<td>" . htmlspecialchars($row['part_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['part_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['genre']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['manufacturer']) . "</td>";
                            echo "<td>£" . number_format($row['unit_price'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['supplier_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reorder_level']) . "</td>";
                            echo "<td class='quantity'>" . htmlspecialchars($row['quantity_in_stock']) . "</td>";
                            echo "<td>
                                    <div class='button-container'>
                                        <button class='update-btn' data-id='{$row['part_id']}' data-action='decrease'>-</button>
                                        <button class='update-btn' data-id='{$row['part_id']}' data-action='increase'>+</button>
                                    </div>
                                  </td>";
                            echo "<td><a href='manage_part.php?part_id=" . htmlspecialchars($row['part_id']) . "' class='manage-btn'>Manage Parts</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No parts found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('include/footer.php'); ?>
    <!-- calling ajax for the updating stock part -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
     <!-- calling all of our js scripts -->
    <script src="js/update_stock.js"></script>
    <script src="js/filter_inventory.js"></script>
    <script src="js/filter_validation.js"></script>
</body>
</html>

