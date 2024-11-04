// Show the edit form with pre-filled data
function showEditForm(id, name, description, price, category) {
    // Set form values
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editPrice').value = price;
    document.getElementById('editCategory').value = category;

    // Display the edit form
    document.getElementById('editForm').style.display = 'block';
}

// Hide the edit form
function hideEditForm() {
    document.getElementById('editForm').style.display = 'none';
}

// Close the edit form when clicking outside of it
window.onclick = function(event) {
    const editForm = document.getElementById('editForm');
    if (event.target == editForm) {
        hideEditForm();
    }
};
