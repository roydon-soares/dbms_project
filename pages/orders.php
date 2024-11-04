<?php
session_start();
require_once '../config/db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch orders and order items from the database
$query = "SELECT orders.id, employees.name AS employee_name, orders.order_date, orders.total_amount 
          FROM orders 
          LEFT JOIN employees ON orders.employee_id = employees.id";
$ordersResult = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../public/assets/css/orders.css">
</head>
<body>
    <div class="orders-container">
        <header>
            <h1>Order Management</h1>
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </header>

        <section class="orders-actions">
            <button class="add-btn" onclick="showAddOrderForm()">+ Add New Order</button>
        </section>

        <!-- Add Order Form (initially hidden) -->
        <div id="addOrderForm" class="form-popup" style="display:none;">
            <h2>Add New Order</h2>
            <form action="../controllers/orderController.php" method="POST">
                <label for="employee_id">Employee</label>
                <select name="employee_id" required>
                    <?php
                    $employeeQuery = "SELECT id, name FROM employees";
                    $employeeResult = $conn->query($employeeQuery);
                    while ($employee = $employeeResult->fetch_assoc()) {
                        echo "<option value='" . $employee['id'] . "'>" . htmlspecialchars($employee['name']) . "</option>";
                    }
                    ?>
                </select>
                <label for="total_amount">Total Amount</label>
                <input type="number" step="0.01" name="total_amount" placeholder="Total Amount" required>
                <button type="submit" name="addOrder" class="submit-btn">Add Order</button>
                <button type="button" onclick="hideAddOrderForm()" class="cancel-btn">Cancel</button>
            </form>
        </div>

        <!-- Orders List -->
        <div class="orders-list">
            <?php if ($ordersResult->num_rows > 0): ?>
                <?php while ($order = $ordersResult->fetch_assoc()): ?>
                    <div class="order-item">
                        <h3>Order #<?php echo $order['id']; ?></h3>
                        <p><strong>Employee:</strong> <?php echo htmlspecialchars($order['employee_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                        <p><strong>Total:</strong> $<?php echo htmlspecialchars($order['total_amount']); ?></p>

                        <!-- Button to show items for each order -->
                        <button onclick="toggleOrderItems(<?php echo $order['id']; ?>)">View Items</button>

                        <!-- Order Items (initially hidden) -->
                        <div id="orderItems<?php echo $order['id']; ?>" class="order-items" style="display: none;">
                            <?php
                            $itemQuery = "SELECT menu_items.name, order_items.quantity, order_items.price 
                                          FROM order_items 
                                          JOIN menu_items ON order_items.menu_item_id = menu_items.id 
                                          WHERE order_items.order_id = " . $order['id'];
                            $itemResult = $conn->query($itemQuery);
                            if ($itemResult->num_rows > 0) {
                                while ($item = $itemResult->fetch_assoc()) {
                                    echo "<p>" . htmlspecialchars($item['name']) . " - " . htmlspecialchars($item['quantity']) . " x $" . htmlspecialchars($item['price']) . "</p>";
                                }
                            } else {
                                echo "<p>No items in this order.</p>";
                            }
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-orders">No orders found. Add new orders above.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../public/assets/js/script.js"></script>
</body>
</html>
