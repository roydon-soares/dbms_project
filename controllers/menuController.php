<?php
require_once '../config/db.php';

// Fetch menu items from the database
function fetchMenuItems($conn) {
    $query = "SELECT * FROM menu_items";
    return $conn->query($query);
}

// Search menu items by name and category
function searchMenuItems($conn, $searchTerm, $categoryTerm) {
    $query = "SELECT * FROM menu_items WHERE name LIKE ? AND (? = '' OR category = ?)";
    $stmt = $conn->prepare($query);
    $likeSearchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param('sss', $likeSearchTerm, $categoryTerm, $categoryTerm);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch distinct categories for dropdown
function fetchCategories($conn) {
    $query = "SELECT DISTINCT category FROM menu_items";
    return $conn->query($query);
}

// Add a new menu item
if (isset($_POST['addMenuItem'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $query = "INSERT INTO menu_items (name, description, price, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssds', $name, $description, $price, $category);

    if ($stmt->execute()) {
        header("Location: ../pages/menu_items.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Edit a menu item
if (isset($_POST['editMenuItem'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $query = "UPDATE menu_items SET name = ?, description = ?, price = ?, category = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssdsi', $name, $description, $price, $category, $id);

    if ($stmt->execute()) {
        header("Location: ../pages/menu_items.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Delete a menu item
if (isset($_POST['deleteMenuItem'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM menu_items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        header("Location: ../pages/menu_items.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
