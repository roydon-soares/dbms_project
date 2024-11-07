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




// Ensure the form was submitted with the necessary POST data

// Assuming connection to the database ($conn) is already established

if (isset($_POST['updateOrder'])) {
    // Retrieve the order ID and other fields from the form submission
    $orderId = $_POST['order_id'];
    $customerName = $_POST['customer_name']; // This is the name submitted by the user
    $employeeId = $_POST['employee_id'];
    $totalAmount = $_POST['total_amount'];

    // Check if menu items and quantities are set, to prevent undefined index warnings
    if (isset($_POST['menu_item_id']) && isset($_POST['quantity'])) {
        $menuItems = $_POST['menu_item_id']; // Array of menu items
        $quantities = $_POST['quantity']; // Array of quantities
    } else {
        $menuItems = [];
        $quantities = [];
    }

    // Step 1: Update the order (excluding menu items)
    // First, retrieve the customer_id from the 'customers' table based on the name
    $getCustomerQuery = "SELECT id FROM customers WHERE name = ?";
    $stmt = $conn->prepare($getCustomerQuery);
    $stmt->bind_param("s", $customerName); // Binding customer name to parameter
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        $customerId = $customer['id'];
    } else {
        echo "Customer not found!";
        exit();
    }

    // Update the order table with the new customer_id, employee_id, and total_amount
    $updateOrderQuery = "UPDATE orders SET customer_id = ?, employee_id = ?, total_amount = ? WHERE id = ?";
    $stmt = $conn->prepare($updateOrderQuery);
    $stmt->bind_param('ssdi', $customerId, $employeeId, $totalAmount, $orderId);

    if ($stmt->execute()) {
        // Step 2: Delete existing order items (this removes any previous menu items)
        $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $conn->prepare($deleteItemsQuery);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();

        // Step 3: Insert the updated menu items and quantities into the order_items table
        if (count($menuItems) > 0) {
            $insertItemQuery = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
            for ($i = 0; $i < count($menuItems); $i++) {
                $menuItemId = $menuItems[$i];
                $quantity = $quantities[$i];

                // Fetch the price of the menu item
                $getMenuItemPriceQuery = "SELECT price FROM menu_items WHERE id = ?";
                $stmt = $conn->prepare($getMenuItemPriceQuery);
                $stmt->bind_param('i', $menuItemId);
                $stmt->execute();
                $menuItemResult = $stmt->get_result();
                if ($menuItemResult->num_rows > 0) {
                    $menuItem = $menuItemResult->fetch_assoc();
                    $price = $menuItem['price'];
                } else {
                    // If menu item not found, skip this item
                    continue;
                }

                // Insert each menu item with its quantity and price
                $stmt = $conn->prepare($insertItemQuery);
                $stmt->bind_param('iiid', $orderId, $menuItemId, $quantity, $price);
                $stmt->execute();
            }
        }

        // After updating, redirect to the orders list
        header('Location: ../pages/orders.php?update=success');
        exit();
    } else {
        // If the update query failed
        echo "Error updating the order: " . $stmt->error;
    }
}
?>