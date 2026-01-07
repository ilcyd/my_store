<?php
// Customer cart page is disabled - redirecting to admin dashboard
header('Location: index.php');
exit;
?>
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$cart_items = getCartItems();
$cart_total = getCartTotal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - My Store</title>
    
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
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .quantity-input {
            width: 80px;
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
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link active" href="cart.php">
                            <i class="bi bi-cart3"></i> Cart
                            <span class="cart-badge" id="cart-count"><?php echo getCartCount(); ?></span>
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> Account
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="customer-dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="register.php">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">Shopping Cart</h2>

        <?php if(empty($cart_items)): ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <h3 class="mt-3">Your cart is empty</h3>
                <p class="text-muted">Start shopping and add some items to your cart!</p>
                <a href="products.php" class="btn btn-primary mt-3">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <?php foreach($cart_items as $item): ?>
                            <div class="row align-items-center border-bottom py-3" id="cart-item-<?php echo $item['id']; ?>">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         class="cart-item-image rounded" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="text-muted small"><?php echo htmlspecialchars($item['category_name']); ?></p>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-0 fw-bold">$<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" 
                                           class="form-control quantity-input" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="<?php echo $item['stock']; ?>"
                                           onchange="updateCartQuantity(<?php echo $item['cart_id']; ?>, this.value)">
                                </div>
                                <div class="col-md-2 text-end">
                                    <p class="mb-2 fw-bold text-primary">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="products.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($cart_total['subtotal'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span><?php echo $cart_total['shipping'] > 0 ? '$' . number_format($cart_total['shipping'], 2) : 'FREE'; ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (<?php echo $cart_total['tax_rate'] * 100; ?>%)</span>
                                <span>$<?php echo number_format($cart_total['tax'], 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong class="text-primary h4">$<?php echo number_format($cart_total['total'], 2); ?></strong>
                            </div>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="checkout.php" class="btn btn-primary w-100 btn-lg">
                                    Proceed to Checkout
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary w-100 btn-lg">
                                    Login to Checkout
                                </a>
                                <p class="text-center text-muted small mt-2">or <a href="register.php">create an account</a></p>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check"></i> Secure checkout
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6>Have a promo code?</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter code" id="promoCode">
                                <button class="btn btn-outline-primary" onclick="applyPromoCode()">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3">My Store</h5>
                    <p>Your trusted online shopping destination for quality products at great prices.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="products.php" class="text-white-50">Products</a></li>
                        <li><a href="categories.php" class="text-white-50">Categories</a></li>
                        <li><a href="about.php" class="text-white-50">About Us</a></li>
                        <li><a href="contact.php" class="text-white-50">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contact Info</h5>
                    <p class="text-white-50">
                        Email: support@mystore.com<br>
                        Phone: (555) 123-4567<br>
                        Address: 123 Shop Street, City, State
                    </p>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> My Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>
