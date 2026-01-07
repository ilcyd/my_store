<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$categories = getAllCategories();
$all_products = getProductsForPOS();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Admin</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="./dist/assets/extensions/sweetalert2/sweetalert2.min.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        .product-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        .product-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .product-card img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .product-card .name {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .product-card .price {
            color: #435ebe;
            font-weight: bold;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-empty {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .quantity-control button {
            width: 30px;
            height: 30px;
            padding: 0;
            font-size: 14px;
        }
        .quantity-control input {
            width: 50px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <script src="dist/assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include 'includes/admin-sidebar.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Point of Sale System</h3>
                            <p class="text-subtitle text-muted">Process customer purchases</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <div class="float-start float-lg-end mb-3">
                                <a href="admin-pos-kiosk.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-display"></i> Kiosk Mode
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <nav aria-label="breadcrumb" class="breadcrumb-header">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">POS</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="alert alert-info mb-3">
                                        <strong>Quick Tips:</strong> Scan barcode below | F1-Focus Scanner | F4-Checkout | F5-Express Checkout (Cash)
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="bi bi-upc-scan"></i> Barcode / SKU Scanner</label>
                                        <input type="text" id="barcodeInput" class="form-control form-control-lg" placeholder="Scan barcode or type SKU and press Enter..." autofocus>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" id="searchProduct" class="form-control" placeholder="🔍 Search products by name...">
                                    </div>
                                    <div class="btn-group mb-3" role="group">
                                        <button type="button" class="btn btn-outline-primary active" onclick="filterCategory('all')">All</button>
                                        <?php foreach($categories as $cat): ?>
                                        <button type="button" class="btn btn-outline-primary" onclick="filterCategory(<?php echo $cat['id']; ?>)"><?php echo htmlspecialchars($cat['name']); ?></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <div id="productGrid" class="product-grid">
                                        <!-- Products will be loaded via AJAX -->
                                        <div class="text-center p-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Current Sale</h5>
                                </div>
                                <div class="card-body" style="min-height: 400px;">
                                    <div id="cartItems"></div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5>Total:</h5>
                                        <h5 id="total">₱0.00</h5>
                                    </div>
                                    <button class="btn btn-warning w-100 mb-2" onclick="expressCheckout()" id="expressBtn" disabled>
                                        <i class="bi bi-lightning-charge-fill"></i> Express Checkout (Cash)
                                    </button>
                                    <button class="btn btn-success w-100 mb-2" onclick="showPaymentModal()" id="checkoutBtn" disabled>
                                        <i class="bi bi-credit-card"></i> Full Checkout
                                    </button>
                                    <button class="btn btn-danger w-100" onclick="clearCart()">
                                        <i class="bi bi-trash"></i> Clear Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p><?php echo date('Y'); ?> &copy; My Store Admin</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Customer Name <small class="text-muted">(Optional)</small></label>
                        <input type="text" class="form-control" id="customerName" placeholder="Walk-in Customer">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Email <small class="text-muted">(Optional)</small></label>
                        <input type="email" class="form-control" id="customerEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Phone <small class="text-muted">(Optional)</small></label>
                        <input type="tel" class="form-control" id="customerPhone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod">
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                        </select>
                    </div>
                    <div class="mb-3" id="cashPaymentSection">
                        <label class="form-label">Amount Received</label>
                        <input type="number" step="0.01" class="form-control form-control-lg" id="amountReceived">
                        <div class="mt-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickAmount(100)">₱100</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickAmount(200)">₱200</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickAmount(500)">₱500</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setQuickAmount(1000)">₱1000</button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="setExactAmount()">Exact Amount</button>
                        </div>
                        <small class="text-muted">Total: <span id="modalTotal">₱0.00</span></small>
                        <div id="changeAmount" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="processPayment()">Complete Sale</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script>
        let cart = [];
        let allProducts = [];
        let currentCategory = 'all';
        let currentSearch = '';
        
        // Load products via AJAX
        function loadProducts() {
            const params = new URLSearchParams();
            if(currentCategory !== 'all') {
                params.append('category', currentCategory);
            }
            if(currentSearch) {
                params.append('search', currentSearch);
            }
            
            fetch('/my_store/api/get-products.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        allProducts = data.products;
                        renderProducts(data.products);
                    } else {
                        console.error('Failed to load products:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    document.getElementById('productGrid').innerHTML = '<div class="text-center text-danger p-4">Failed to load products</div>';
                });
        }
        
        // Render products in the grid
        function renderProducts(products) {
            const grid = document.getElementById('productGrid');
            
            if(products.length === 0) {
                grid.innerHTML = '<div class="text-center text-muted p-4">No products found</div>';
                return;
            }
            
            grid.innerHTML = products.map(product => `
                <div class="product-card" data-category="${product.category_id}" onclick="addToCart(${product.id}, '${escapeHtml(product.name)}', ${product.price}, '${escapeHtml(product.image)}')">
                    <img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}">
                    <div class="name">${escapeHtml(product.name)}</div>
                    <div class="price">₱${parseFloat(product.price).toFixed(2)}</div>
                </div>
            `).join('');
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Load products on page load and refresh every 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            setInterval(loadProducts, 10000);
        });
        
        // Barcode scanner
        document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                const sku = this.value.trim().toUpperCase();
                if(sku) {
                    addProductBySKU(sku);
                    this.value = '';
                }
            }
        });
        
        function addProductBySKU(sku) {
            const product = allProducts.find(p => p.sku.toUpperCase() === sku);
            
            if(product) {
                addToCart(product.id, product.name, parseFloat(product.price), product.image);
                document.getElementById('barcodeInput').style.borderColor = '#28a745';
                setTimeout(() => { document.getElementById('barcodeInput').style.borderColor = ''; }, 500);
            } else {
                document.getElementById('barcodeInput').style.borderColor = '#dc3545';
                Swal.fire({
                    icon: 'error',
                    title: 'Product Not Found',
                    text: 'SKU: ' + sku,
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => { document.getElementById('barcodeInput').style.borderColor = ''; }, 1000);
            }
        }
        
        function setQuickAmount(amount) {
            document.getElementById('amountReceived').value = amount;
            document.getElementById('amountReceived').dispatchEvent(new Event('input'));
        }
        
        function setExactAmount() {
            const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
            document.getElementById('amountReceived').value = total.toFixed(2);
            document.getElementById('amountReceived').dispatchEvent(new Event('input'));
        }
        
        function expressCheckout() {
            if(cart.length === 0) return;
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            Swal.fire({
                title: 'Express Checkout',
                html: '<div style="font-size: 1.5rem; font-weight: bold; margin: 20px 0;">Total: ₱' + total.toFixed(2) + '</div>' +
                      '<p>Complete cash payment for walk-in customer?</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Complete Sale',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    const orderData = {
                        customer_name: 'Walk-in Customer',
                        customer_email: 'walk-in@store.com',
                        customer_phone: 'N/A',
                        shipping_address: 'Store Pickup',
                        shipping_city: 'N/A',
                        shipping_state: 'N/A',
                        shipping_zip: '00000',
                        payment_method: 'cash',
                        items: cart,
                        subtotal: total,
                        tax: 0,
                        shipping: 0,
                        total: total
                    };
                    
                    fetch('/my_store/api/admin-process-pos-sale.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(orderData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Server error: ' + response.status);
                        }
                        return response.text();
                    })
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            if(data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sale Complete!',
                                    html: '<div style="font-size: 1.5rem; margin: 20px 0;">Order #' + data.order_id + '</div>' +
                                          '<div style="font-size: 2rem; font-weight: bold; color: #28a745;">₱' + total.toFixed(2) + '</div>',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    cart = [];
                                    updateCart();
                                    document.getElementById('barcodeInput').focus();
                                });
                            } else {
                                Swal.fire('Error', data.message || 'Failed to process sale', 'error');
                            }
                        } catch(e) {
                            console.error('JSON Parse Error:', e);
                            console.error('Server Response:', text);
                            Swal.fire('Error', 'Invalid server response. Check console for details.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to connect to server. Please check your connection.', 'error');
                    });
                }
            });
        }
        
        function addToCart(id, name, price, image) {
            const existingItem = cart.find(item => item.id === id);
            if(existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ id, name, price, image, quantity: 1 });
            }
            updateCart();
        }
        
        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCart();
        }
        
        function updateQuantity(id, quantity) {
            const item = cart.find(item => item.id === id);
            if(item) {
                item.quantity = Math.max(1, parseInt(quantity));
                updateCart();
            }
        }
        
        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            
            if(cart.length === 0) {
                cartItems.innerHTML = '<div class="cart-empty"><i class="bi bi-cart-x" style="font-size: 3rem;"></i><p>Cart is empty</p></div>';
                document.getElementById('checkoutBtn').disabled = true;
                document.getElementById('expressBtn').disabled = true;
            } else {
                let html = '';
                cart.forEach(item => {
                    html += `
                        <div class="cart-item">
                            <div style="flex: 1;">
                                <strong>${item.name}</strong><br>
                                <small>₱${item.price.toFixed(2)} each</small>
                            </div>
                            <div class="quantity-control">
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                <input type="number" value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)" min="1">
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                            </div>
                            <div style="width: 80px; text-align: right;">
                                <strong>₱${(item.price * item.quantity).toFixed(2)}</strong>
                            </div>
                            <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                });
                cartItems.innerHTML = html;
                document.getElementById('checkoutBtn').disabled = false;
                document.getElementById('expressBtn').disabled = false;
            }
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('total').textContent = '₱' + total.toFixed(2);
        }
        
        function clearCart() {
            if(cart.length === 0) return;
            
            Swal.fire({
                title: 'Clear Cart?',
                text: 'This will remove all items from the cart.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, clear it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    cart = [];
                    updateCart();
                }
            });
        }
        
        function showPaymentModal() {
            if(cart.length === 0) return;
            
            const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
            document.getElementById('modalTotal').textContent = '₱' + total.toFixed(2);
            
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }
        
        document.getElementById('paymentMethod').addEventListener('change', function() {
            const cashSection = document.getElementById('cashPaymentSection');
            if(this.value === 'cash') {
                cashSection.style.display = 'block';
            } else {
                cashSection.style.display = 'none';
            }
        });
        
        document.getElementById('amountReceived').addEventListener('input', function() {
            const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
            const received = parseFloat(this.value) || 0;
            const change = received - total;
            
            const changeDiv = document.getElementById('changeAmount');
            if(received >= total) {
                changeDiv.innerHTML = '<div class="alert alert-success">Change: ₱' + change.toFixed(2) + '</div>';
            } else {
                changeDiv.innerHTML = '<div class="alert alert-danger">Insufficient amount</div>';
            }
        });
        
        function processPayment() {
            const customerName = document.getElementById('customerName').value || 'Walk-in Customer';
            const paymentMethod = document.getElementById('paymentMethod').value;
            
            if(paymentMethod === 'cash') {
                const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
                const received = parseFloat(document.getElementById('amountReceived').value) || 0;
                if(received < total) {
                    Swal.fire('Error', 'Insufficient payment amount', 'error');
                    return;
                }
            }
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            const orderData = {
                customer_name: customerName,
                customer_email: document.getElementById('customerEmail').value || 'walk-in@store.com',
                customer_phone: document.getElementById('customerPhone').value || 'N/A',
                shipping_address: 'Store Pickup',
                shipping_city: 'N/A',
                shipping_state: 'N/A',
                shipping_zip: '00000',
                payment_method: paymentMethod,
                items: cart,
                subtotal: total,
                tax: 0,
                shipping: 0,
                total: total
            };
            
            fetch('/my_store/api/admin-process-pos-sale.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if(data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sale Complete!',
                            text: 'Order #' + data.order_id + ' has been created.',
                            showCancelButton: true,
                            confirmButtonText: 'View Receipt',
                            cancelButtonText: 'New Sale'
                        }).then((result) => {
                            if(result.isConfirmed) {
                                window.open('admin-order-detail.php?id=' + data.order_id, '_blank');
                            }
                            cart = [];
                            updateCart();
                            document.getElementById('customerName').value = '';
                            document.getElementById('customerEmail').value = '';
                            document.getElementById('customerPhone').value = '';
                            document.getElementById('amountReceived').value = '';
                            document.getElementById('changeAmount').innerHTML = '';
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to process sale', 'error');
                    }
                } catch(e) {
                    console.error('JSON Parse Error:', e);
                    console.error('Server Response:', text);
                    Swal.fire('Error', 'Invalid server response. Check console for details.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to connect to server. Please check your connection.', 'error');
            });
        }
        
        function filterCategory(categoryId) {
            currentCategory = categoryId;
            loadProducts();
            
            const buttons = document.querySelectorAll('.btn-group button');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
        
        document.getElementById('searchProduct').addEventListener('input', function() {
            currentSearch = this.value.toLowerCase();
            loadProducts();
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Don't trigger shortcuts when typing in modals
            if(document.querySelector('.modal.show')) return;
            
            // F1 - Focus barcode scanner
            if(e.key === 'F1') {
                e.preventDefault();
                document.getElementById('barcodeInput').focus();
            }
            // F2 - Focus search
            if(e.key === 'F2') {
                e.preventDefault();
                document.getElementById('searchProduct').focus();
            }
            // F4 - Checkout
            if(e.key === 'F4') {
                e.preventDefault();
                if(cart.length > 0) showPaymentModal();
            }
            // F5 - Express Checkout
            if(e.key === 'F5') {
                e.preventDefault();
                if(cart.length > 0) expressCheckout();
            }
            // F8 - Clear cart
            if(e.key === 'F8') {
                e.preventDefault();
                clearCart();
            }
        });
        
        updateCart();
    </script>
</body>
</html>
