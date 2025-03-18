async function updateQuantity(productId, change) {
    try {
        const response = await fetch('cart_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=update&product_id=${productId}&change=${change}`
        });

        const data = await response.json();
        if (data.success) {
            document.getElementById(`quantity-${productId}`).textContent = data.quantity;
            location.reload(); // Refresh cart to update totals
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('An error occurred while updating the cart.');
    }
}

async function removeItem(productId) {
    try {
        const response = await fetch('cart_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=remove&product_id=${productId}`
        });

        const data = await response.json();
        if (data.success) {
            location.reload(); // Refresh cart after removing an item
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('An error occurred while removing the item.');
    }
}

async function clearCart() {
    try {
        const response = await fetch('cart_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=clear'
        });

        const data = await response.json();
        if (data.success) location.reload();
        else alert(data.message);
    } catch (error) {
        alert('An error occurred while clearing the cart.');
    }
}
