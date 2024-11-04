script.js
function showAddForm() {
    document.getElementById("addForm").style.display = "block";
}

function hideAddForm() {
    document.getElementById("addForm").style.display = "none";
}

function showAddOrderForm() {
    document.getElementById("addOrderForm").style.display = "block";
}

function hideAddOrderForm() {
    document.getElementById("addOrderForm").style.display = "none";
}

function toggleOrderItems(orderId) {
    var itemsDiv = document.getElementById("orderItems" + orderId);
    if (itemsDiv.style.display === "none") {
        itemsDiv.style.display = "block";
    } else {
        itemsDiv.style.display = "none";
    }

}
function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
}

function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
}


function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
}

function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
}

function showEditForm(id, name, description, price, category) {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editPrice').value = price;
    document.getElementById('editCategory').value = category;
    document.getElementById('editForm').style.display = 'block';
}

function hideEditForm() {
    document.getElementById('editForm').style.display = 'none';
}
function showAddOrderForm() {
    document.getElementById('addOrderForm').style.display = 'block';
}

function hideAddOrderForm() {
    document.getElementById('addOrderForm').style.display = 'none';
}

function toggleOrderItems(orderId) {
    var orderItemsDiv = document.getElementById('orderItems' + orderId);
    if (orderItemsDiv.style.display === 'none' || orderItemsDiv.style.display === '') {
        orderItemsDiv.style.display = 'block';
    } else {
        orderItemsDiv.style.display = 'none';
    }
}
