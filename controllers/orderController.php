<?php
session_start();
require_once '../config/db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle adding a new order
if (isset($_POST['addOrder'])) {
    $employee_id = $_POST['employee_id'];
    $menu_item_id = $_POST['menu_item_id'];
    $quantity = $_POST['quantity'];
    $total_amount = $_POST['total_amount'];
    
    // Calculate total amount if needed based on menu item price
    $itemQuery = "SELECT price FROM menu_items WHERE id = ?";
    $stmt = $conn->prepare($itemQuery);
    $stmt->bind_param("i", $menu_item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $menuItem = $result->fetch_assoc();
    
    if ($menuItem) {
        $price = $menuItem['price'];
        $calculatedTotal = $price * $quantity;

        // Insert order into orders table
        $insertOrder = "INSERT INTO orders (employee_id, total_amount) VALUES (?, ?)";
        $stmt = $conn->prepare($insertOrder);
        $stmt->bind_param("id", $employee_id, $calculatedTotal);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get last inserted order ID

        // Insert order items into order_items table
        $insertOrderItem = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertOrderItem);
        $stmt->bind_param("iiid", $order_id, $menu_item_id, $quantity, $price);
        $stmt->execute();
        
        // Redirect back to orders page
        header("Location: ../pages/orders.php");
        exit();
    }
}
?>
