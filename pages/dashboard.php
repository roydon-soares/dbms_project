<?php
session_start();
require_once '../config/db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch total menu items
$menuQuery = "SELECT COUNT(*) AS total_menu_items FROM menu_items";
$menuResult = $conn->query($menuQuery);
$menuData = $menuResult->fetch_assoc();
$total_menu_items = $menuData['total_menu_items'];

// Fetch total orders
$orderQuery = "SELECT COUNT(*) AS total_orders FROM orders";
$orderResult = $conn->query($orderQuery);
$orderData = $orderResult->fetch_assoc();
$total_orders = $orderData['total_orders'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../public/assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <section class="stats">
            <div class="stat-item">
                <h2>Total Menu Items</h2>
                <p><?php echo $total_menu_items; ?></p>
            </div>
            <div class="stat-item">
                <h2>Total Orders</h2>
                <p><?php echo $total_orders; ?></p>
            </div>
        </section>

        <section class="actions">
            <a href="menu_items.php" class="action-btn">Manage Menu Items</a>
            <a href="orders.php" class="action-btn">Manage Orders</a>
            <a href="register.php" class="action-btn">Register Employees</a> <!-- New Register button -->
        </section>
    </div>
</body>
</html>
