<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/menuController.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch menu items from the database
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$categoryTerm = isset($_GET['category']) ? $_GET['category'] : '';
$menuItems = searchMenuItems($conn, $searchTerm, $categoryTerm);

// Fetch distinct categories for dropdown
$categories = fetchCategories($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu Items</title>
    <link rel="stylesheet" href="../public/assets/css/menu_items.css">
</head>
<body style="background: url('../public/assets/images/mfc.jpg') no-repeat center center fixed; background-size: cover;">
    <div class="menu-container">
        <header>
            <h1>Menu Management</h1>
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </header>

        <!-- Search Bar -->
        <section class="search-bar">
            <form action="menu_items.php" method="GET">
                <input type="text" name="search" placeholder="Search by item name" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($category['category']); ?>" <?php if ($categoryTerm == $category['category']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Search</button>
            </form>
        </section>

        <!-- Add Item Section -->
        <section class="menu-actions">
            <h2>Add New Menu Item</h2>
            <form action="../controllers/menuController.php" method="POST" class="menu-form">
                <input type="text" name="name" placeholder="Item Name" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="text" name="category" placeholder="Category">
                <button type="submit" name="addMenuItem" class="btn-primary">Add Item</button>
            </form>
        </section>

        <!-- Edit Item Form (initially hidden) -->
        <div id="editForm" class="form-popup" style="display:none;">
            <h2>Edit Menu Item</h2>
            <form action="../controllers/menuController.php" method="POST" id="editFormContent" class="menu-form">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="name" id="editName" placeholder="Item Name" required>
                <textarea name="description" id="editDescription" placeholder="Description"></textarea>
                <input type="number" step="0.01" name="price" id="editPrice" placeholder="Price" required>
                <input type="text" name="category" id="editCategory" placeholder="Category">
                <button type="submit" name="editMenuItem" class="btn-primary">Save Changes</button>
                <button type="button" onclick="hideEditForm()" class="btn-secondary">Cancel</button>
            </form>
        </div>

        <!-- Menu Items Table -->
        <div class="menu-table">
            <?php if ($menuItems->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $menuItems->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>
                                    <button onclick="showEditForm(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['description']); ?>', <?php echo $row['price']; ?>, '<?php echo addslashes($row['category']); ?>')" class="btn-primary">Edit</button>
                                    <form action="../controllers/menuController.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="deleteMenuItem" class="btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-items">No menu items found for the search terms "<?php echo htmlspecialchars($searchTerm); ?>" and category "<?php echo htmlspecialchars($categoryTerm); ?>".</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../public/assets/js/menu_items.js"></script>
</body>
</html>
