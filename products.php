<?php
// Customer products page is disabled - redirecting to admin dashboard
header('Location: index.php');
exit;
?>
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get filters from URL
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 12;

// Get products based on filters
$products = getProducts($category_id, $search, $sort, $page, $per_page);
$total_products = getTotalProducts($category_id, $search);
$total_pages = ceil($total_products / $per_page);
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - My Store</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/iconly.css">
    <style>
        .product-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
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
        .filter-sidebar {
            position: sticky;
            top: 100px;
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
                        <a class="nav-link active" href="products.php">Products</a>
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
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Products</h2>
                <p class="text-muted">Showing <?php echo count($products); ?> of <?php echo $total_products; ?> products</p>
            </div>
            <div class="col-md-6">
                <form class="d-flex" method="GET" action="products.php">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="filter-sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Filters</h5>
                        </div>
                        <div class="card-body">
                            <!-- Categories Filter -->
                            <h6 class="mb-3">Categories</h6>
                            <div class="list-group mb-4">
                                <a href="products.php" class="list-group-item list-group-item-action <?php echo is_null($category_id) ? 'active' : ''; ?>">
                                    All Categories
                                </a>
                                <?php foreach($categories as $category): ?>
                                <a href="products.php?category=<?php echo $category['id']; ?>" 
                                   class="list-group-item list-group-item-action <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                    <span class="badge bg-secondary float-end"><?php echo $category['product_count']; ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>

                            <!-- Sort Filter -->
                            <h6 class="mb-3">Sort By</h6>
                            <select class="form-select" id="sortSelect" onchange="location.href='products.php?sort=' + this.value + '<?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>'">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="row g-4">
                    <?php if(empty($products)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <h4>No products found</h4>
                                <p>Try adjusting your search or filters</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach($products as $product): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card product-card h-100" onclick="location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                                <?php if($product['stock'] <= 0): ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-danger">Out of Stock</span>
                                    </div>
                                <?php elseif($product['featured']): ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-warning">Featured</span>
                                    </div>
                                <?php endif; ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     class="card-img-top product-image" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-primary mb-2 align-self-start"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo substr(htmlspecialchars($product['description']), 0, 80); ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="h5 text-primary mb-0">$<?php echo number_format($product['price'], 2); ?></span>
                                        <?php if($product['stock'] > 0): ?>
                                            <button class="btn btn-primary btn-sm" 
                                                    onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>)">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                Out of Stock
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&sort=<?php echo $sort; ?>">Previous</a>
                        </li>
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&sort=<?php echo $sort; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
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
    <script src="assets/js/cart.js"></script>
</body>
</html>
