function showAddOrderForm() {
    document.getElementById('addOrderForm').style.display = 'block';
}

function hideAddOrderForm() {
    document.getElementById('addOrderForm').style.display = 'none';
}

function addOrderItem() {
    var container = document.getElementById('orderItemsContainer');
    var newItem = document.createElement('div');
    newItem.className = 'order-item';
    newItem.innerHTML = `
        <select name="menu_item_id[]" required>
            <option value="">Select Menu Item</option>
            <?php
            $categoryQuery = "SELECT id, name, category FROM menu_items ORDER BY category";
            $categoryResult->data_seek(0); // Reset the result pointer to the beginning
            $currentCategory = '';
            while ($item = $categoryResult->fetch_assoc()) {
                if ($item['category'] !== $currentCategory) {
                    if ($currentCategory !== '') {
                        echo '</optgroup>';
                    }
                    $currentCategory = $item['category'];
                    echo "<optgroup label='" . htmlspecialchars($currentCategory) . "'>";
                }
                echo "<option value='" . $item['id'] . "'>" . htmlspecialchars($item['name']) . "</option>";
            }
            if ($currentCategory !== '') {
                echo '</optgroup>';
            }
            ?>
        </select>
        <input type="number" name="quantity[]" min="1" placeholder="Quantity" required>
    `;
    container.appendChild(newItem);
}

function toggleOrderItems(orderId) {
    var itemsDiv = document.getElementById('orderItems' + orderId);
    if (itemsDiv.style.display === 'none') {
        itemsDiv.style.display = 'block';
    } else {
        itemsDiv.style.display = 'none';
    }
}

