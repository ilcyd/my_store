<?php
// Customer checkout page is disabled - redirecting to admin dashboard
header('Location: index.php');
exit;
?>
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cart_items = getCartItems();
$cart_total = getCartTotal();

if(empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - My Store</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/iconly.css">
    <style>
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
        .checkout-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 15px;
            position: relative;
        }
        .step.active {
            color: #435ebe;
            font-weight: bold;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 25px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: #e9ecef;
            z-index: -1;
        }
        .step:last-child::after {
            display: none;
        }
        .step.active::after {
            background: #435ebe;
        }
    </style>
</head>
<body>
    <script src="dist/assets/static/js/initTheme.js"></script>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-shop"></i> My Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart3"></i> Cart
                            <span class="cart-badge" id="cart-count"><?php echo getCartCount(); ?></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="customer-dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">Checkout</h2>

        <!-- Checkout Steps -->
        <div class="checkout-steps">
            <div class="step active">
                <i class="bi bi-1-circle-fill"></i><br>
                Shipping
            </div>
            <div class="step">
                <i class="bi bi-2-circle"></i><br>
                Payment
            </div>
            <div class="step">
                <i class="bi bi-3-circle"></i><br>
                Review
            </div>
        </div>

        <form id="checkoutForm" method="POST" action="api/process-order.php">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Shipping Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country *</label>
                                    <select class="form-select" name="country" required>
                                        <option value="USA">United States</option>
                                        <option value="Canada">Canada</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Address *</label>
                                    <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" placeholder="Street address" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">State/Province *</label>
                                    <input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ZIP/Postal Code *</label>
                                    <input type="text" class="form-control" name="zip" value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                    <label class="form-check-label" for="credit_card">
                                        <i class="bi bi-credit-card"></i> Credit/Debit Card
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="bi bi-paypal"></i> PayPal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                    <label class="form-check-label" for="cod">
                                        <i class="bi bi-cash"></i> Cash on Delivery
                                    </label>
                                </div>
                            </div>

                            <div id="cardDetails">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Card Number</label>
                                        <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CVV</label>
                                        <input type="text" class="form-control" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Notes (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" name="notes" rows="3" placeholder="Special instructions for delivery..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <?php foreach($cart_items as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <div>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($cart_total['subtotal'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span><?php echo $cart_total['shipping'] > 0 ? '$' . number_format($cart_total['shipping'], 2) : 'FREE'; ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax</span>
                                <span>$<?php echo number_format($cart_total['tax'], 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total</strong>
                                <strong class="text-primary h4">$<?php echo number_format($cart_total['total'], 2); ?></strong>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                Place Order
                            </button>

                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check"></i> Your payment information is secure
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="mb-3">We Accept</h6>
                            <div class="d-flex gap-2">
                                <i class="bi bi-credit-card-2-front display-6"></i>
                                <i class="bi bi-paypal display-6"></i>
                                <i class="bi bi-cash display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> My Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api/process-order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed!',
                        text: 'Your order #' + data.order_id + ' has been placed successfully.',
                        confirmButtonText: 'View Order'
                    }).then(() => {
                        window.location.href = 'order-confirmation.php?id=' + data.order_id;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to process order. Please try again.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            });
        });

        // Show/hide card details based on payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('cardDetails').style.display = 
                    this.value === 'credit_card' ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
