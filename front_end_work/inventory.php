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
$branch_filter = isset($_POST['branch']) ? $_POST['branch'] : '';

//Fetch branches for the dropdown
$branches_sql = "SELECT branch_id, branch_name FROM branches";
$branches_result = $con->query($branches_sql);
$branches = [];
if ($branches_result->num_rows > 0) {
    while ($branch = $branches_result->fetch_assoc()) {
        $branches[] = $branch;
    }
}

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
            suppliers.supplier_name,
            branches.branch_name
        FROM parts 
        INNER JOIN suppliers ON parts.supplier_id = suppliers.supplier_id
        LEFT JOIN branches ON parts.branch_id = branches.branch_id";

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
if (!empty($branch_filter)) {
    $conditions[] = "parts.branch_id = '$branch_filter'";
}

//if the conditions array is NOT empty we IMPLODE ("Join array elements with a string") this to the previously written sql statement
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
//adding the ORDER BY clause (this is always added, ust kept breaking when everywhere else but here
$sql .= " ORDER BY $sort_column $sort_order";

//finally we execute the query :)
$result = $con->query($sql);

// Delete part
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_part'])) {
    $part_id = $_POST['part_id'];

    //begin transaction to ensure data consistency
    $con->begin_transaction();

    try {
        //Delete all items in the order
        $delete_items_sql = "DELETE FROM order_items WHERE part_id = ?";
        $delete_items_stmt = $con->prepare($delete_items_sql);
        $delete_items_stmt->bind_param('i', $part_id);
        if (!$delete_items_stmt->execute()) {
            throw new Exception('Error deleting item from order items.');
        }

        //Delete the order itself
        $delete_part_sql = "DELETE FROM parts WHERE part_id = ?";
        $delete_part_stmt = $con->prepare($delete_part_sql);
        $delete_part_stmt->bind_param('i', $part_id);
        if (!$delete_part_stmt->execute()) {
            throw new Exception('Error deleting the parts.');
        }

        //Commit the transaction
        $con->commit();
        logAction($user_data['user_id'], $user_data['username'], 'DELETE', "Deleted Part ID: $part_id"); // Log the action here
        header('Location: inventory.php'); //Redirect to parts page after successful removal
        exit;
    } catch (Exception $e) {
        //Rollback in case of error
        $con->rollback();
        echo "<p>Error removing part: " . $e->getMessage() . "</p>";
    }
}
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

        <!-- Export Inventory as PDF Button -->
        <div class="button-container">
            <a href="generate_inventory_report.php" class="export-btn">Export Inventory as PDF</a>
        </div>

        <!-- Filter Form -->
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

                    <input type="checkbox" id="suspension" name="genre[]" value="Suspension" <?php echo in_array('Suspension', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="suspension">Suspension</label>

                    <input type="checkbox" id="electrical" name="genre[]" value="Electrical" <?php echo in_array('Electrical', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="electrical">Electrical</label>

                    <input type="checkbox" id="cooling" name="genre[]" value="Cooling" <?php echo in_array('Cooling', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="cooling">Cooling</label>

                    <input type="checkbox" id="fuel" name="genre[]" value="Fuel" <?php echo in_array('Fuel', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="fuel">Fuel</label>

                    <input type="checkbox" id="Accessories" name="genre[]" value="Accessories" <?php echo in_array('Accessories', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="Accessories">Accessories</label>

                    <input type="checkbox" id="interior" name="genre[]" value="Interior" <?php echo in_array('Interior', $genre_filter) ? 'checked' : ''; ?>>
                    <label for="interior">Interior</label>
                </div>
            </div>
            <!-- branch filter section -->
            <div class="filters-branch">
                <label for="branch">Filter by Branch:</label>
                <select id="branch" name="branch">
                    <option value="">All Branches</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>" <?php echo (isset($_POST['branch']) && $_POST['branch'] == $branch['branch_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($branch['branch_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- a search box section -->
            <div class="filters-search">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" placeholder="Search parts..." value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
            </div>

            <!-- apply filter button/ this is disabled by default but is enabled by js when the user actually selects a filter to apply, this is cause it kept breaking -->
            <button type="submit" id="apply-filters">Apply Filters</button> 
        </form>

        <!-- Add a Part Button -->
        <div id="add-part-button-container" class="button-container">
            <a href="adding_new_parts.php" class="add-part-btn">Add Part</a> 
        </div>

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
                        <th><a href="?sort_column=branch_name&sort_order=<?php echo $next_sort_order; ?>">Branch</a></th>
                        <th><a href="?sort_column=reorder_level&sort_order=<?php echo $next_sort_order; ?>">Reorder Level</a></th>
                        <th><a href="?sort_column=quantity_in_stock&sort_order=<?php echo $next_sort_order; ?>">Quantity in Stock</a></th>
                        <th>Update Stock Levels</th>
                        <th>Manage Part Details</th>
                        <th>Remove Part</th>
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
                            echo "<td>" . htmlspecialchars($row['branch_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reorder_level']) . "</td>";
                            echo "<td class='quantity'>" . htmlspecialchars($row['quantity_in_stock']) . "</td>";
                            echo "<td>
                                    <div class='button-container'>
                                        <button class='update-btn' data-id='{$row['part_id']}' data-action='decrease'>-</button>
                                        <button class='update-btn' data-id='{$row['part_id']}' data-action='increase'>+</button>
                                    </div>
                                  </td>";
                            echo "<td><a href='manage_part.php?part_id=" . htmlspecialchars($row['part_id']) . "' class='manage-btn'>Manage Part</a></td>";
                            echo "<td>
                                    <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this part?\");'>
                                        <input type='hidden' name='part_id' value='" . htmlspecialchars($row['part_id']) . "'>
                                        <button type='submit' name='remove_part' class='remove-btn'>Remove Part</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13'>No parts found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Include JavaScript files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/update_stock.js"></script>
</body>
</html>