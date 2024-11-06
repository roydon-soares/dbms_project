document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");
});

function showAddOrderForm() {
    document.getElementById("addOrderForm").style.display = "block";
}

function hideAddOrderForm() {
    document.getElementById("addOrderForm").style.display = "none";
}

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
    `;
    container.appendChild(newItem);
}

function showUpdateOrderForm(orderId) {
    var orderRow = document.querySelector(`tr[data-order-id='${orderId}']`);
    var customerName = orderRow.querySelector('.customer-name').textContent;
    var employeeId = orderRow.querySelector('.employee-id').dataset.employeeId;
    var totalAmount = orderRow.querySelector('.total-amount').textContent;

    document.getElementById('updateOrderId').value = orderId;
    document.getElementById('updateCustomerName').value = customerName;
    document.getElementById('updateEmployeeId').value = employeeId;
    document.getElementById('updateTotalAmount').value = totalAmount;

    document.getElementById('updateOrderForm').style.display = 'block';
}

function hideUpdateOrderForm() {
    document.getElementById('updateOrderForm').style.display = 'none';
}

function toggleOrderItems(orderId) {
    var itemsDiv = document.getElementById("orderItems" + orderId);
    if (itemsDiv.style.display === "none") {
        itemsDiv.style.display = "block";
    } else {
        itemsDiv.style.display = "none";
    }
}
