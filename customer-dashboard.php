<?php
// Customer dashboard is disabled - redirecting to admin dashboard
header('Location: index.php');
exit;
?>

$user = getUserById($_SESSION['user_id']);
$recent_orders = getRecentOrders($_SESSION['user_id'], 5);
$order_stats = getOrderStats($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - My Store</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/iconly.css">
</head>
<body>
    <script src="dist/assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="index.php"><img src="./dist/assets/compiled/svg/logo.svg" alt="Logo"></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                            </div>
                        </div>
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>
                        <li class="sidebar-item active">
                            <a href="customer-dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="orders.php" class='sidebar-link'>
                                <i class="bi bi-receipt"></i>
                                <span>My Orders</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="profile.php" class='sidebar-link'>
                                <i class="bi bi-person"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="index.php" class='sidebar-link'>
                                <i class="bi bi-shop"></i>
                                <span>Continue Shopping</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="logout.php" class='sidebar-link'>
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            
            <div class="page-heading">
                <h3>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h3>
            </div>
            
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-9">
                        <!-- Stats Cards -->
                        <div class="row">
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon purple mb-2">
                                                    <i class="iconly-boldBuy"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Total Orders</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $order_stats['total_orders']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon blue mb-2">
                                                    <i class="iconly-boldActivity"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Pending</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $order_stats['pending_orders']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon green mb-2">
                                                    <i class="iconly-boldTick-Square"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Completed</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $order_stats['completed_orders']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                <div class="stats-icon red mb-2">
                                                    <i class="iconly-boldWallet"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Total Spent</h6>
                                                <h6 class="font-extrabold mb-0">$<?php echo number_format($order_stats['total_spent'], 2); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Orders -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Recent Orders</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>Date</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(empty($recent_orders)): ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">No orders yet. <a href="products.php">Start shopping!</a></td>
                                                        </tr>
                                                    <?php else: ?>
                                                        <?php foreach($recent_orders as $order): ?>
                                                        <tr>
                                                            <td>#<?php echo $order['id']; ?></td>
                                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                            <td>$<?php echo number_format($order['total'], 2); ?></td>
                                                            <td>
                                                                <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                                                    <?php echo ucfirst($order['status']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                                    View Details
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if(!empty($recent_orders)): ?>
                                        <div class="text-center mt-3">
                                            <a href="orders.php" class="btn btn-primary">View All Orders</a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <!-- Account Info -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Account Info</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="avatar avatar-xl mb-3">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&size=128" alt="Avatar">
                                    </div>
                                    <h5><?php echo htmlspecialchars($user['name']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <small class="text-muted">Member Since</small><br>
                                    <strong><?php echo date('M Y', strtotime($user['created_at'])); ?></strong>
                                </div>
                                <a href="profile.php" class="btn btn-primary w-100">Edit Profile</a>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Quick Actions</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="products.php" class="btn btn-outline-primary">Browse Products</a>
                                    <a href="cart.php" class="btn btn-outline-primary">View Cart</a>
                                    <a href="orders.php" class="btn btn-outline-primary">Track Orders</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p><?php echo date('Y'); ?> &copy; My Store</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="dist/assets/static/js/components/dark.js"></script>
    <script src="dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="dist/assets/compiled/js/app.js"></script>
</body>
</html>
