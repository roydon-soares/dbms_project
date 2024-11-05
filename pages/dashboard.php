<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Sample data for demonstration purposes
$total_menu_items = 24; // Replace with actual query result
$total_orders = 120; // Replace with actual query result
$pending_orders = 8; // Replace with actual query result
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
            <div class="stat-item">
                <h2>Pending Orders</h2>
                <p><?php echo $pending_orders; ?></p>
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
