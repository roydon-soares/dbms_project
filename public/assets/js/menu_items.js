function showEditForm(id, name, description, price, category) {
    document.getElementById('editForm').style.display = 'block';
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editPrice').value = price;
    document.getElementById('editCategory').value = category;
}


function hideEditForm() {
    document.getElementById('editForm').style.display = 'none';
}

window.onclick = function(event) {
    var editForm = document.getElementById('editForm');
    if (event.target === editForm) {
        hideEditForm();
    }
};
