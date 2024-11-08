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
    // Get the order ID from the URL
    $orderId = $_GET['order_id'];

    // Begin a transaction to ensure both deletions are executed properly
    $conn->begin_transaction();

    try {
        // Prepare and execute the query to delete order items first (referential integrity)
        $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $conn->prepare($deleteItemsQuery);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        // Now, delete the order itself
        $deleteOrderQuery = "DELETE FROM orders WHERE id = ?";
        $stmt = $conn->prepare($deleteOrderQuery);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        // Commit the transaction after successful execution
        $conn->commit();

        // Return success response (JSON or redirect as needed)
        header("Location: ..pages/orders.php");  // Redirect to the orders page after successful deletion
        exit();
    } catch (Exception $e) {
        // If there is an error, rollback the transaction
        $conn->rollback();
        // Return error response
        echo "Error deleting order: " . $e->getMessage();
        exit();
    }
} else {
    // If 'action' or 'order_id' is not set, return an error
    echo "Invalid request. Missing action or order_id.";
    exit();
}




// Ensure the form was submitted with the necessary POST data

// Assuming connection to the database ($conn) is already established

if (isset($_POST['updateOrder'])) {
    // Retrieve the order ID and other fields from the form submission
    $orderId = $_POST['order_id'];
    $customerName = $_POST['customer_name']; // This is the name submitted by the user
    $employeeId = $_POST['employee_id'];
    $totalAmount = $_POST['total_amount'];
    $customerEmail = $_POST['customer_email'];  // Assuming there's a customer_email input

    // Check if menu items and quantities are set, to prevent undefined index warnings
    if (isset($_POST['menu_item_id']) && isset($_POST['quantity']) && count($_POST['menu_item_id']) > 0) {
        $menu_item_ids = $_POST['menu_item_id'];
        $quantities = $_POST['quantity'];

        // Check if the customer exists or update their information
        $customerQuery = "SELECT id FROM customers WHERE name = ? AND email = ?";
        $stmt = $conn->prepare($customerQuery);
        $stmt->bind_param("ss", $customerName, $customerEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Customer exists, fetch their ID
            $customer = $result->fetch_assoc();
            $customerId = $customer['id'];
        } else {
            // Customer doesn't exist, insert them into the database
            $insertCustomer = "INSERT INTO customers (name, email) VALUES (?, ?)";
            $stmt = $conn->prepare($insertCustomer);
            $stmt->bind_param("ss", $customerName, $customerEmail);
            $stmt->execute();
            $customerId = $stmt->insert_id;
        }

        // Update the existing order
        $updateOrder = "UPDATE orders SET customer_id = ?, employee_id = ?, total_amount = ? WHERE id = ?";
        $stmt = $conn->prepare($updateOrder);
        $stmt->bind_param("iidi", $customerId, $employeeId, $totalAmount, $orderId);
        $stmt->execute();

        // Delete existing order items and re-insert new ones
        $deleteOrderItems = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $conn->prepare($deleteOrderItems);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        // Reset the total amount
        $totalAmount = 0;

        // Insert each order item
        for ($i = 0; $i < count($menu_item_ids); $i++) {
            $menu_item_id = $menu_item_ids[$i];
            $quantity = $quantities[$i];

            // Get the price for the menu item
            $itemQuery = "SELECT price FROM menu_items WHERE id = ?";
            $stmt = $conn->prepare($itemQuery);
            $stmt->bind_param("i", $menu_item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $menuItem = $result->fetch_assoc();

            if ($menuItem) {
                $price = $menuItem['price'];
                $calculatedTotal = $price * $quantity;
                $totalAmount += $calculatedTotal;

                // Insert into order_items table
                $insertOrderItem = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insertOrderItem);
                $stmt->bind_param("iiid", $orderId, $menu_item_id, $quantity, $price);
                $stmt->execute();
            }
        }

        // Update the order total amount again after processing items
        $updateOrderTotal = "UPDATE orders SET total_amount = ? WHERE id = ?";
        $stmt = $conn->prepare($updateOrderTotal);
        $stmt->bind_param("di", $totalAmount, $orderId);
        $stmt->execute();

        // Redirect or display success message
        header("Location: ../pages/orders.php");
        exit();

    } else {
        // Handle error: no menu items or quantities were provided
        echo "Error: No menu items or quantities were submitted.";
        exit();
    }
}





?>