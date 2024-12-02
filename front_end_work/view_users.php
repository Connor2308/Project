<?php
include('include/init.php'); //initalise everything like user data
//verifying admin?
checkAdmin();
//Sorting Section
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'user_id'; //if nothing is in the sort column eg when we load up the page we will sort in the part_id
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC'; //this ensures that when the page is first loaded it ensures that it is set to ASC order 
$next_sort_order = ($sort_order == 'ASC') ? 'DESC' : 'ASC'; // this bit is for toggling between ASC and DESC order

//sql
$sql = "SELECT 
        users.user_id,
        users.username,
        users.password,
        users.email,
        users.role,
        user_details.first_name,
        user_details.last_name,
        user_details.phone_number
    FROM 
        users
    LEFT JOIN 
        user_details 
    ON 
        users.user_id = user_details.user_id
    ORDER BY 
        $sort_column $sort_order";
//result of the query
$result = $con->query($sql);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Users</title>
</head>
<body>
    <?php include('include/header.php')?>
    <div class="page-container">
        <h2 class="page-title">System's Users</h2>

        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <!-- here are the table headers with the the next sort order button in there aswell so if they click it then the order will change -->
                        <th><a href="?sort_column=user_id&sort_order=<?php echo $next_sort_order; ?>">User ID</a></th>
                        <th><a href="?sort_column=username&sort_order=<?php echo $next_sort_order; ?>">Username</a></th>
                        <th><a href="?sort_column=email&sort_order=<?php echo $next_sort_order; ?>">Email</a></th>
                        <th><a href="?sort_column=role&sort_order=<?php echo $next_sort_order; ?>">Role</a></th>
                        <th><a href="?sort_column=first_name&sort_order=<?php echo $next_sort_order; ?>">First Name</a></th>
                        <th><a href="?sort_column=last_name&sort_order=<?php echo $next_sort_order; ?>">Last Name</a></th>
                        <th><a href="?sort_column=phone_number&sort_order=<?php echo $next_sort_order; ?>">Phone Number</a></th>
                        <th>Manage Users</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- actually fill the table with each row of data the sql fetches from the db -->
                    <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr id='row-{$row['user_id']}'>";
                                echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['first_name'] ?? "N/A") . "</td>";
                                echo "<td>" . htmlspecialchars($row['last_name'] ?? "N/A") . "</td>";
                                echo "<td>" . htmlspecialchars($row['phone_number'] ?? "N/A") . "</td>";
                                echo "<td><a href='manage_users.php?user_id=" . htmlspecialchars($row['user_id']) . "' class='manage-btn'>Manage User</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No users found.</td></tr>";
                        }
                        ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>