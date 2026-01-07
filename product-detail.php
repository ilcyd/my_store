<?php
// Customer product detail page is disabled - redirecting to admin dashboard
header('Location: index.php');
exit;
?>
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($product_id);

if(!$product) {
    header('Location: products.php');
    exit;
}

$related_products = getRelatedProducts($product['category_id'], $product_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - My Store</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/iconly.css">
    <style>
        .product-image-main {
            height: 500px;
            object-fit: cover;
            width: 100%;
            border-radius: 10px;
        }
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
        .product-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            height: 200px;
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
                        <a class="nav-link" href="cart.php">
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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <li class="breadcrumb-item"><a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                     class="product-image-main mb-4" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="mb-3">
                    <span class="badge bg-primary"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <?php if($product['featured']): ?>
                        <span class="badge bg-warning">Featured</span>
                    <?php endif; ?>
                </div>
                
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="mb-4">
                    <span class="h2 text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                </div>

                <div class="mb-4">
                    <?php if($product['stock'] > 0): ?>
                        <span class="badge bg-success">In Stock (<?php echo $product['stock']; ?> available)</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <?php if($product['stock'] > 0): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label class="form-label">Quantity</label>
                                <input type="number" 
                                       class="form-control quantity-input" 
                                       id="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="<?php echo $product['stock']; ?>">
                            </div>
                            <div class="col-md-8">
                                <button class="btn btn-primary btn-lg w-100" onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value)">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> This product is currently out of stock.
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <h6>Product Details</h6>
                    <ul class="list-unstyled">
                        <li><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></li>
                        <li><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></li>
                        <li><strong>Availability:</strong> <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if(!empty($related_products)): ?>
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row g-4">
                <?php foreach($related_products as $related): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100" onclick="location.href='product-detail.php?id=<?php echo $related['id']; ?>'">
                        <img src="<?php echo htmlspecialchars($related['image']); ?>" 
                             class="card-img-top product-image" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">$<?php echo number_format($related['price'], 2); ?></span>
                                <?php if($related['stock'] > 0): ?>
                                    <button class="btn btn-primary btn-sm" 
                                            onclick="event.stopPropagation(); addToCart(<?php echo $related['id']; ?>)">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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
