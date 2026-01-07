// Cart functionality
function addToCart(productId, quantity = 1) {
    fetch('api/cart-add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Update cart count
            updateCartCount();
            
            // Show success message
            if(typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: 'Product has been added to your cart.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Product added to cart!');
            }
        } else {
            if(typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to add product to cart.'
                });
            } else {
                alert('Failed to add product to cart.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function updateCartQuantity(cartId, quantity) {
    fetch('api/cart-update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_id: cartId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Failed to update cart.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function removeFromCart(cartId) {
    if(typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Remove Item?',
            text: 'Are you sure you want to remove this item from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                performRemoveFromCart(cartId);
            }
        });
    } else {
        if(confirm('Remove this item from your cart?')) {
            performRemoveFromCart(cartId);
        }
    }
}

function performRemoveFromCart(cartId) {
    fetch('api/cart-remove.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_id: cartId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Failed to remove item from cart.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function updateCartCount() {
    fetch('api/cart-count.php')
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const cartBadge = document.getElementById('cart-count');
            if(cartBadge) {
                cartBadge.textContent = data.count;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function applyPromoCode() {
    const code = document.getElementById('promoCode').value;
    
    if(!code) {
        alert('Please enter a promo code.');
        return;
    }
    
    fetch('api/apply-promo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            code: code
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Promo Code Applied!',
                    text: 'You saved $' + data.discount.toFixed(2),
                }).then(() => {
                    location.reload();
                });
            } else {
                alert('Promo code applied! You saved $' + data.discount.toFixed(2));
                location.reload();
            }
        } else {
            if(typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Code',
                    text: data.message || 'Invalid or expired promo code.'
                });
            } else {
                alert('Invalid or expired promo code.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
