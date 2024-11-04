<?php
require_once '../config/db.php';

if (isset($_POST['addOrder'])) {
    $employee_id = $_POST['employee_id'];
    $total_amount = $_POST['total_amount'];
    $order_date = date('Y-m-d H:i:s'); // Current date and time

    $query = "INSERT INTO orders (employee_id, order_date, total_amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isd', $employee_id, $order_date, $total_amount);

    if ($stmt->execute()) {
        header("Location: ../pages/orders.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

if (isset($_POST['deleteOrder'])) {
    $order_id = $_POST['order_id'];

    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);

    if ($stmt->execute()) {
        header("Location: ../pages/orders.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
