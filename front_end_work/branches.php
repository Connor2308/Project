
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/base.css">
    <link rel="stylesheet" href="style/branch.css">
    <link rel="stylesheet" href="style/tablelist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branches</title>
</head>
<body>
    <?php include('include/header.php')?>
    <div class="page-container">
        <h2 class="page-title">Branches</h2>
        
        <!-- Add Supplier Form -->
        <form action="branches.php" method="POST" class="branch-form">
            <h3>Add New Branch</h3>
            <!-- <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?> -->
            <div class="branch-form-columns">
                <div class="left-column">
                    <div class="form-group">
                        <label for="supplier_name">Branches Name:</label>
                        <input type="text" id="branch_name" name="branch_name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_name">Contact Name:</label>
                        <input type="text" id="contact_name" name="contact_name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_phone">Contact Phone:</label>
                        <input type="text" id="contact_phone" name="contact_phone" required>
                    </div>
                </div>

                <div class="right-column">
                    <div class="form-group">
                        <label for="contact_email">Contact Email:</label>
                        <input type="email" id="contact_email" name="contact_email" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                </div>
            </div>
            <button type="submit" name="add_supplier" class="submit-btn">Add Branch</button>
        </form>

        <!-- Branches Table -->
        <div class="table-container">
            <table class="inventory-list">
                <thead>
                    <tr>
                        <th><a href="?sort_column=supplier_id&sort_order=<?php echo $next_sort_order; ?>">Branch ID</a></th>
                        <th><a href="?sort_column=supplier_name&sort_order=<?php echo $next_sort_order; ?>">Branch Name</a></th>
                        <th><a href="?sort_column=contact_name&sort_order=<?php echo $next_sort_order; ?>">Contact Name</a></th>
                        <th><a href="?sort_column=contact_phone&sort_order=<?php echo $next_sort_order; ?>">Contact Phone</a></th>
                        <th><a href="?sort_column=contact_email&sort_order=<?php echo $next_sort_order; ?>">Contact Email</a></th>
                        <th><a href="?sort_column=address&sort_order=<?php echo $next_sort_order; ?>">Address</a></th>
                        <th>Manage Branches</th>
                        <th>Delete Branches</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['branch_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_email']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><a href="manage_branch.php?branch_id=<?php echo $row['branch_id']; ?>" class="manage-btn">Manage Branch</a></td>
                                <td><a href="branches.php?deactivate_branch_id=<?php echo $row['branch_id']; ?>" class="remove-btn" onclick="return confirm('Are you sure you want to deactivate this branch?');">Deactivate Branch</a></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No branches found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include('include/footer.php')?>
</body>
</html>