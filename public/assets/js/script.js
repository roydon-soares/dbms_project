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
