<?php
// Customer order detail page is disabled - redirecting to admin dashboard
header('Location: index.php');
exit;
?>
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = getOrderById($order_id, $_SESSION['user_id']);

if(!$order) {
    header('Location: orders.php');
    exit;
}

$order_items = getOrderItems($order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order['id']; ?> - My Store</title>
    
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
        .product-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
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
                            <i class="bi bi-person-circle"></i> Account
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
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Order Details</h2>
                <p class="text-muted">Order #<?php echo $order['id']; ?> placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
            </div>
            <div class="col-md-6 text-end">
                <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?> fs-5">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     class="product-image-small rounded me-3" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                                    <small class="text-muted">SKU: <?php echo htmlspecialchars($item['sku']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Shipping Address</h6>
                                <p class="mb-0">
                                    <?php echo htmlspecialchars($order['shipping_name']); ?><br>
                                    <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                    <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                    <?php echo htmlspecialchars($order['shipping_state']); ?> 
                                    <?php echo htmlspecialchars($order['shipping_zip']); ?><br>
                                    <?php echo htmlspecialchars($order['shipping_country']); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Contact Information</h6>
                                <p class="mb-0">
                                    <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($order['shipping_email']); ?><br>
                                    <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($order['shipping_phone']); ?>
                                </p>
                            </div>
                        </div>
                        <?php if(!empty($order['notes'])): ?>
                        <hr>
                        <h6>Order Notes</h6>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>$<?php echo number_format($order['shipping'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <span>$<?php echo number_format($order['tax'], 2); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong class="text-primary h4">$<?php echo number_format($order['total'], 2); ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Payment Method:</strong><br>
                            <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?>
                        </p>
                        <p>
                            <strong>Payment Status:</strong><br>
                            <span class="badge bg-<?php echo $order['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <a href="orders.php" class="btn btn-outline-primary">Back to Orders</a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print Order
                    </button>
                </div>
            </div>
        </div>
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
</body>
</html>
