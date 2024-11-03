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

        <section class="menu-actions">
            <button class="add-btn" onclick="showAddForm()">+ Add New Item</button>
        </section>

        <!-- Add Item Form (initially hidden) -->
        <div id="addForm" class="form-popup">
            <h2>Add New Menu Item</h2>
            <form action="../controllers/menuController.php" method="POST">
                <input type="text" name="name" placeholder="Item Name" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="text" name="category" placeholder="Category">
                <button type="submit" name="addMenuItem" class="submit-btn">Add Item</button>
                <button type="button" onclick="hideAddForm()" class="cancel-btn">Cancel</button>
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
                            <button onclick="editItem(<?php echo $row['id']; ?>)">Edit</button>
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

    <script src="../public/assets/js/scripts.js"></script>
</body>
</html>