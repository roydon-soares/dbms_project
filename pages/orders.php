<?php
session_start();
require_once '../config/db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch orders and order items from the database
$query = "SELECT orders.id, employees.name AS employee_name, customers.name AS customer_name, orders.order_date, orders.total_amount 
          FROM orders 
          LEFT JOIN employees ON orders.employee_id = employees.id
          LEFT JOIN customers ON orders.customer_id = customers.id";
$ordersResult = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../public/assets/css/orders.css">
    <script>
        function showAddOrderForm() {
            document.getElementById("addOrderForm").style.display = "block";
        }

        function hideAddOrderForm() {
            document.getElementById("addOrderForm").style.display = "none";
        }

        function showUpdateOrderForm(orderId, customerName, employeeId, menuItems) {
            document.getElementById("updateOrderForm").style.display = "block";
            document.getElementById("updateOrderId").value = orderId;
            document.getElementById("updateCustomerName").value = customerName;
            document.getElementById("updateEmployeeId").value = employeeId;

            // Clear existing menu items
            const container = document.getElementById("updateOrderItemsContainer");
            container.innerHTML = '';

            // Add menu items
            menuItems.forEach(item => {
                const newItem = document.createElement("div");
                newItem.className = "order-item";
                newItem.innerHTML = `
                    <select name="menu_item_id[]" required>
                        <option value="">Select Menu Item</option>
                        ${getMenuItems(item.menu_item_id)}
                    </select>
                    <input type="number" name="quantity[]" min="1" value="${item.quantity}" required>
                `;
                container.appendChild(newItem);
            });
        }

        function hideUpdateOrderForm() {
            document.getElementById("updateOrderForm").style.display = "none";
        }

        function addOrderItem() {
            var container = document.getElementById("orderItemsContainer");
            var newItem = document.createElement("div");
            newItem.className = "order-item";
            newItem.innerHTML = `
                <select name="menu_item_id[]" required>
                    <option value="">Select Menu Item</option>
                    ${getMenuItems()}
                </select>
                <input type="number" name="quantity[]" min="1" placeholder="Quantity" required>
            `;
            container.appendChild(newItem);
        }

        function getMenuItems(selectedItemId = null) {
            var menuItems = '';
            <?php
            $categoryQuery = "SELECT id, name, category FROM menu_items ORDER BY category";
            $categoryResult = $conn->query($categoryQuery);
            $currentCategory = '';
            while ($item = $categoryResult->fetch_assoc()) {
                if ($item['category'] !== $currentCategory) {
                    if ($currentCategory !== '') {
                        echo 'menuItems += "</optgroup>";'; // Close previous optgroup
                    }
                    $currentCategory = $item['category'];
                    echo 'menuItems += "<optgroup label=\'' . htmlspecialchars($currentCategory) . '\'>";';
                }
                echo 'menuItems += "<option value=\'' . $item['id'] . '\' ' . ($item['id'] == ' + selectedItemId + ' ? "selected" : "") . '>' . htmlspecialchars($item['name']) . '</option>";';
            }
            if ($currentCategory !== '') {
                echo 'menuItems += "</optgroup>";'; // Close the last optgroup
            }
            ?>
            return menuItems;
        }

        function toggleOrderItems(orderId) {
            var itemsDiv = document.getElementById("orderItems" + orderId);
            if (itemsDiv.style.display === "none") {
                itemsDiv.style.display = "block";
            } else {
                itemsDiv.style.display = "none";
            }
        }
    </script>
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
                <label for="customer_name">Customer Name</label>
                <input type="text" name="customer_name" placeholder="Customer Name" required>
                
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

                <div id="orderItemsContainer">
                    <label>Menu Items</label>
                    <div class="order-item">
                        <select name="menu_item_id[]" required>
                            <option value="">Select Menu Item</option>
                            <?php
                            // Fetch menu items categorized
                            $categoryQuery = "SELECT id, name, category FROM menu_items ORDER BY category";
                            $categoryResult = $conn->query($categoryQuery);
                            $currentCategory = '';
                            while ($item = $categoryResult->fetch_assoc()) {
                                // Add category heading if it's a new category
                                if ($item['category'] !== $currentCategory) {
                                    if ($currentCategory !== '') {
                                        echo '</optgroup>'; // Close previous optgroup
                                    }
                                    $currentCategory = $item['category'];
                                    echo "<optgroup label='" . htmlspecialchars($currentCategory) . "'>";
                                }
                                echo "<option value='" . $item['id'] . "'>" . htmlspecialchars($item['name']) . "</option>";
                            }
                            if ($currentCategory !== '') {
                                echo '</optgroup>'; // Close the last optgroup
                            }
                            ?>
                        </select>
                        <input type="number" name="quantity[]" min="1" placeholder="Quantity" required>
                    </div>
                </div>
                
                <button type="button" onclick="addOrderItem()" class="add-item-btn">Add Another Item</button>
                
                <button type="submit" name="addOrder" class="submit-btn">Add Order</button>
                <button type="button" onclick="hideAddOrderForm()" class="cancel-btn">Cancel</button>
            </form>
        </div>

        <!-- Update Order Form (initially hidden) -->
        <div id="updateOrderForm" class="form-popup" style="display:none;"> <h2>Update Order</h2> <form id="updateOrderFormInner" action="../controllers/orderController.php" method="POST"> <input type="hidden" name="order_id" id="updateOrderId" value=""> <label for="update_customer_name">Customer Name</label> <input type="text" name="customer_name" id="updateCustomerName" placeholder="Customer Name" required> <label for="update_employee_id">Employee</label> <select name="employee_id" id="updateEmployeeId" required> <?php $employeeQuery = "SELECT id, name FROM employees"; $employeeResult = $conn->query($employeeQuery); while ($employee = $employeeResult->fetch_assoc()) { echo "<option value='" . $employee['id'] . "'>" . htmlspecialchars($employee['name']) . "</option>"; } ?> </select> <label for="update_total_amount">Total Amount</label> <input type="number" step="0.01" name="total_amount" id="updateTotalAmount" placeholder="Total Amount" required> <button type="submit" name="updateOrder" class="submit-btn">Update Order</button> <button type="button" onclick="hideUpdateOrderForm()" class="cancel-btn">Cancel</button> </form> </div>
        

        <!-- Orders List in Tabular Form -->
        <div class="orders-list">
            <?php if ($ordersResult->num_rows > 0): ?>
                <table>
                    <thead>

                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $ordersResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['employee_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>
                                <td>
                                    
                                
    <!-- Button to show items for each order -->
    <button onclick="toggleOrderItems(<?php echo $order['id']; ?>)">View Items</button>
    
    <!-- Update Order Button -->
   <!-- Update Order Button --> <button onclick="showUpdateOrderForm(<?php echo $order['id']; ?>)">Update</button>
    
    <!-- Delete Order Button -->
    <a href="/orderController.php?action=delete&order_id=<?php echo $order['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
</td>

                            </tr>
                            <tr id="orderItems<?php echo $order['id']; ?>" class="order-items" style="display: none;">
                                <td colspan="6">
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
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-orders">No orders found. Add new orders above.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
