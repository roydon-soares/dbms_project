// Show Update Order Form and populate data
function showUpdateOrderForm(orderId, customerName, employeeId, menuItems) {
    // Show the form
    document.getElementById("updateOrderForm").style.display = "block";
    
    // Populate the form fields
    document.getElementById("updateOrderId").value = orderId;
    document.getElementById("updateCustomerName").value = customerName || '';  // Fix: Ensure customerName is not undefined
    document.getElementById("updateEmployeeId").value = employeeId;
    
    // Populate menu items
    const container = document.getElementById("updateOrderItemsContainer");
    container.innerHTML = ''; // Clear existing items

    // Add each menu item to the form
    menuItems.forEach((item, index) => {
        const newItem = document.createElement("div");
        newItem.className = "order-item";
        newItem.setAttribute("data-index", index);  // Set data-index for easy removal

        newItem.innerHTML = `
            <div class="form-group">
                <select name="menu_item_id[]" required>
                    <option value="">Select Menu Item</option>
                    ${getMenuItems(item.menu_item_id)}  <!-- Populate menu items dynamically -->
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="quantity[]" min="1" value="${item.quantity}" required>
            </div>
            <button type="button" onclick="removeUpdateOrderItem(${index})" class="delete-item-btn">Delete</button>
        `;
        container.appendChild(newItem);
    });
}

// Hide Update Order Form
function hideUpdateOrderForm() {
    document.getElementById("updateOrderForm").style.display = "none";
}

// Add Another Menu Item (for updating)
function addUpdateOrderItem() {
    const container = document.getElementById("updateOrderItemsContainer");
    const newItem = document.createElement("div");
    newItem.className = "order-item";

    newItem.innerHTML = `
        <div class="form-group">
            <select name="menu_item_id[]" required>
                <option value="">Select Menu Item</option>
                ${getMenuItems()}  <!-- Populate menu items dynamically -->
            </select>
        </div>
        <div class="form-group">
            <input type="number" name="quantity[]" min="1" placeholder="Quantity" required>
        </div>
        <button type="button" onclick="removeUpdateOrderItem()" class="delete-item-btn">Delete</button>
    `;
    container.appendChild(newItem);
}

// Remove Menu Item from the Update Form
function removeUpdateOrderItem(index = null) {
    const container = document.getElementById("updateOrderItemsContainer");
    
    // If index is provided, remove the specific item
    if (index !== null) {
        const itemToRemove = container.querySelectorAll('.order-item')[index];
        itemToRemove.remove();
    } else {
        // Remove last added item
        const items = container.querySelectorAll('.order-item');
        if (items.length > 0) {
            items[items.length - 1].remove();
        }
    }
}

// Function to fetch menu items for select options
function getMenuItems(selectedItemId = null) {
    let menuItemsHtml = '';
    
    // Assuming this function fetches menu items dynamically via an API or AJAX
    // Replace this part with the actual implementation for fetching menu items

    return menuItemsHtml;
}

// Utility function to toggle the visibility of order items in the table (if required)
function toggleOrderItems(orderId) {
    const itemsDiv = document.getElementById("orderItems" + orderId);
    if (itemsDiv.style.display === "none") {
        itemsDiv.style.display = "block";
    } else {
        itemsDiv.style.display = "none";
    }
}
