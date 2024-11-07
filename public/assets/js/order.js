document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");
});

// Show Add Order Form
function showAddOrderForm() {
    document.getElementById("addOrderForm").style.display = "block";
}

// Hide Add Order Form
function hideAddOrderForm() {
    document.getElementById("addOrderForm").style.display = "none";
}

// Add a new item to the order
function addOrderItem() {
    var container = document.getElementById("orderItemsContainer");
    var newItem = document.createElement("div");
    newItem.className = "order-item";
    newItem.innerHTML = `
        <select name="menu_item_id[]" required>
            <option value="">Select Menu Item</option>
            ${document.getElementById('menuItemsOptions').innerHTML}
        </select>
        <input type="number" name="quantity[]" min="1" placeholder="Quantity" required>
        <button type="button" onclick="removeOrderItem(this)">Remove</button>
    `;
    container.appendChild(newItem);
}

// Remove an item from the order
function removeOrderItem(button) {
    var itemDiv = button.closest('.order-item');
    itemDiv.remove();
}

// Show Update Order Form
function showUpdateOrderForm(orderId) {
    var orderRow = document.querySelector(`tr[data-order-id='${orderId}']`);
    var employeeId = orderRow.querySelector('.employee-id').dataset.employeeId;
    var totalAmount = orderRow.querySelector('.total-amount').textContent;

    // Set existing values to the update form fields
    document.getElementById('updateOrderId').value = orderId;
    document.getElementById('updateEmployeeId').value = employeeId;
    document.getElementById('updateTotalAmount').value = totalAmount;

    // Show the update form
    document.getElementById('updateOrderForm').style.display = 'block';

    // Optionally load existing order items into the form
    loadOrderItems(orderId);
}

// Hide Update Order Form
function hideUpdateOrderForm() {
    document.getElementById('updateOrderForm').style.display = 'none';
}

// Toggle visibility of order items
function toggleOrderItems(orderId) {
    var itemsDiv = document.getElementById("orderItems" + orderId);
    if (itemsDiv.style.display === "none") {
        itemsDiv.style.display = "block";
    } else {
        itemsDiv.style.display = "none";
    }
}

// Load existing order items into the update form
function loadOrderItems(orderId) {
    var itemsContainer = document.getElementById('updateOrderItemsContainer');
    itemsContainer.innerHTML = ''; // Clear existing items

    // Assuming each order item is a <div> with details, we'll fetch and display them
    var orderRow = document.querySelector(`tr[data-order-id='${orderId}']`);
    var orderItems = orderRow.querySelectorAll('.order-item'); // Custom class for order items in the row

    orderItems.forEach(item => {
        var menuItemId = item.dataset.menuItemId;
        var quantity = item.dataset.quantity;

        // Create a form element for each item
        var orderItemDiv = document.createElement('div');
        orderItemDiv.className = 'order-item';

        orderItemDiv.innerHTML = `
            <select name="menu_item_id[]" required>
                <option value="">Select Menu Item</option>
                ${document.getElementById('menuItemsOptions').innerHTML}
            </select>
            <input type="number" name="quantity[]" value="${quantity}" min="1" placeholder="Quantity" required>
            <button type="button" onclick="removeOrderItem(this)">Remove</button>
        `;
        itemsContainer.appendChild(orderItemDiv);
    });
}

// Update the order when the form is submitted
function updateOrder() {
    var orderId = document.getElementById('updateOrderId').value;
    var employeeId = document.getElementById('updateEmployeeId').value;
    var totalAmount = document.getElementById('updateTotalAmount').value;
    var orderItems = document.querySelectorAll('#updateOrderItemsContainer .order-item');

    // Collect all order item data (menu_item_id and quantity)
    var items = [];
    var updatedTotalAmount = 0;
    orderItems.forEach(item => {
        var menuItemId = item.querySelector('select').value;
        var quantity = item.querySelector('input').value;

        if (menuItemId && quantity) {
            items.push({ menuItemId, quantity });

            // Assuming we have a function to get the price of an item based on its ID
            var itemPrice = getItemPrice(menuItemId); // This needs to be defined based on your app logic
            updatedTotalAmount += itemPrice * quantity;
        }
    });

    // Update the total amount field with the recalculated total
    document.getElementById('updateTotalAmount').value = updatedTotalAmount;

    // Perform any validation here (e.g., check that fields are filled, etc.)

    // Send the updated data to the server via an API call or form submission
    // For now, just log the data to the console
    console.log({
        orderId,
        employeeId,
        totalAmount: updatedTotalAmount,
        items
    });

    // Hide the update form after submission
    hideUpdateOrderForm();
}

// Placeholder function for getting item price (this should be replaced with actual logic)
function getItemPrice(menuItemId) {
    // Example: you can define a price lookup here
    // For now, returning a dummy value
    var prices = {
        'item1': 10,
        'item2': 15,
        'item3': 20,
    };

    return prices[menuItemId] || 0; // Returns 0 if itemId is not found
}
