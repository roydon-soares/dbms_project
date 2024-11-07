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
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];  // Make sure you have a field for customer email
    $employee_id = $_POST['employee_id'];
    $menu_item_ids = $_POST['menu_item_id'];
    $quantities = $_POST['quantity'];

    // Check if the customer already exists
    $customerQuery = "SELECT id FROM customers WHERE email = ?";
    $stmt = $conn->prepare($customerQuery);
    $stmt->bind_param("s", $customer_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Customer already exists, get the customer id
        $customer = $result->fetch_assoc();
        $customer_id = $customer['id'];
    } else {
        // Insert new customer
        $insertCustomer = "INSERT INTO customers (name, email) VALUES (?, ?)";
        $stmt = $conn->prepare($insertCustomer);
        $stmt->bind_param("ss", $customer_name, $customer_email);
        $stmt->execute();
        $customer_id = $stmt->insert_id;
    }

    // Insert order into orders table
    $insertOrder = "INSERT INTO orders (customer_id, employee_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insertOrder);
    $stmt->bind_param("ii", $customer_id, $employee_id);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get last inserted order ID
    
    $total_amount = 0;

    // Insert order items into order_items table
    for ($i = 0; $i < count($menu_item_ids); $i++) {
        $menu_item_id = $menu_item_ids[$i];
        $quantity = $quantities[$i];

        $itemQuery = "SELECT price FROM menu_items WHERE id = ?";
        $stmt = $conn->prepare($itemQuery);
        $stmt->bind_param("i", $menu_item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $menuItem = $result->fetch_assoc();

        if ($menuItem) {
            $price = $menuItem['price'];
            $calculatedTotal = $price * $quantity;
            $total_amount += $calculatedTotal;

            $insertOrderItem = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertOrderItem);
            $stmt->bind_param("iiid", $order_id, $menu_item_id, $quantity, $price);
            $stmt->execute();
        }
    }

    // Update the total amount in the orders table
    $updateOrder = "UPDATE orders SET total_amount = ? WHERE id = ?";
    $stmt = $conn->prepare($updateOrder);
    $stmt->bind_param("di", $total_amount, $order_id);
    $stmt->execute();
    
    // Redirect back to orders page
    header("Location: ../pages/orders.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
    
    // Delete the order items first (to maintain referential integrity)
    $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($deleteItemsQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    
    // Now, delete the order itself
    $deleteOrderQuery = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($deleteOrderQuery);
    $stmt->bind_param("i", $orderId);
    if ($stmt->execute()) {
        header("Location: ../pages/orders.php");  // Redirect to orders page after successful deletion
        exit();
    } else {
        echo "Error deleting the order.";
    }
}

if (isset($_POST['updateOrder'])) {
    $orderId = $_POST['order_id'];
    $customerName = $_POST['customer_name'];
    $employeeId = $_POST['employee_id'];
    $menuItemIds = $_POST['menu_item_id'];
    $quantities = $_POST['quantity'];

    // Update the order details
    $updateOrderQuery = "UPDATE orders SET customer_id = ?, employee_id = ? WHERE id = ?";
    $stmt = $conn->prepare($updateOrderQuery);
    $stmt->bind_param("iii", $customerId, $employeeId, $orderId);
    $stmt->execute();

    // Update order items
    // First, delete old items
    $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($deleteItemsQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    // Insert updated items
    foreach ($menuItemIds as $index => $menuItemId) {
        $quantity = $quantities[$index];
        $insertItemQuery = "INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertItemQuery);
        $stmt->bind_param("iii", $orderId, $menuItemId, $quantity);
        $stmt->execute();
    }

    // Redirect to orders page after successful update
    header("Location: ../pages/orders.php");
    exit();
}
?>
