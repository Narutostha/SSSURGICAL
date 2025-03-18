document.addEventListener('DOMContentLoaded', function () {
    // Add click event listeners to all Add-to-Cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();

            // Get the product ID from the button's data attribute
            const productId = this.getAttribute('data-product-id');
            if (!productId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Product ID is missing',
                });
                return;
            }

            // Get quantity (default to 1 if not found)
            const quantityInput = document.getElementById(`quantity_${productId}`) || 
                                  document.getElementById('quantity');
            const quantity = quantityInput ? quantityInput.value : 1;

            try {
                // Disable button to prevent multiple clicks
                button.disabled = true;

                // Create FormData
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('product_id', productId);
                formData.append('quantity', quantity);

                // Send the request to the server
                const response = await fetch('cart_process.php', {
                    method: 'POST',
                    body: formData,
                });

                // Check response status
                if (!response.ok) {
                    throw new Error('Failed to communicate with the server.');
                }

                const data = await response.json();
                console.log('Response:', data); // Debug log

                // Handle success or error response
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    // Update the cart count if provided
                    if (data.cart_count !== undefined) {
                        const cartCountElement = document.querySelector('.cart-count');
                        if (cartCountElement) {
                            cartCountElement.textContent = data.cart_count;
                        } else {
                            // Create cart count badge if it doesn't exist
                            const cartIcon = document.querySelector('.cart-icon a');
                            if (cartIcon) {
                                const span = document.createElement('span');
                                span.className = 'cart-count';
                                span.textContent = data.cart_count;
                                cartIcon.appendChild(span);
                            }
                        }
                    }
                } else {
                    throw new Error(data.message || 'Failed to add item to the cart.');
                }
            } catch (error) {
                console.error('Error:', error); // Debug log
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while adding to cart.',
                });
            } finally {
                // Re-enable the button
                button.disabled = false;
            }
        });
    });
});
