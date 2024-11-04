<?php
session_start();
require_once '../config/db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch menu items from the database
$query = "SELECT * FROM menu_items";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu Items</title>
    <link rel="stylesheet" href="../public/assets/css/menu_items.css">
</head>
<body>
    <div class="menu-container">
        <header>
            <h1>Menu Management</h1>
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </header>

        <!-- Add Item Form -->
        <section class="menu-actions">
            <h2>Add New Menu Item</h2>
            <form action="../controllers/menuController.php" method="POST">
                <input type="text" name="name" placeholder="Item Name" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="text" name="category" placeholder="Category">
                <button type="submit" name="addMenuItem" class="submit-btn">Add Item</button>
            </form>
        </section>
        <!-- Edit Item Form (initially hidden) -->
        <div id="editForm" class="form-popup" style="display:none;">
            <h2>Edit Menu Item</h2>
            <form action="../controllers/menuController.php" method="POST" id="editFormContent">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="name" id="editName" placeholder="Item Name" required>
                <textarea name="description" id="editDescription" placeholder="Description"></textarea>
                <input type="number" step="0.01" name="price" id="editPrice" placeholder="Price" required>
                <input type="text" name="category" id="editCategory" placeholder="Category">
                <button type="submit" name="editMenuItem" class="submit-btn">Save Changes</button>
                <button type="button" onclick="hideEditForm()" class="cancel-btn">Cancel</button>
            </form>
        </div>

        <!-- Menu Items List -->
        <div class="menu-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="menu-item">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <p><strong>Price:</strong> $<?php echo htmlspecialchars($row['price']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                        <div class="item-actions">
                        <button onclick="showEditForm(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['description']); ?>', <?php echo $row['price']; ?>, '<?php echo addslashes($row['category']); ?>')">Edit</button>

                            <form action="../controllers/menuController.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="deleteMenuItem" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-items">No menu items found. Add new items above.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../public/assets/js/script.js"></script>
</body>
</html>
