<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

if(isset($_GET['id'])) {
    $customer_id = intval($_GET['id']);
    $customer = getUserById($customer_id);
    $orders = getUserOrders($customer_id);
    $stats = getCustomerStats($customer_id);
    
    if(!$customer) {
        header('Location: admin-customers.php');
        exit;
    }
} else {
    header('Location: admin-customers.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($customer['name']); ?> - Customer Details</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/iconly.css">
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
                            <h3><?php echo htmlspecialchars($customer['name']); ?></h3>
                            <p class="text-subtitle text-muted">Customer details and order history</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="admin-customers.php">Customers</a></li>
                                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($customer['name']); ?></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong><br><?php echo htmlspecialchars($customer['name']); ?></p>
                                    <p><strong>Email:</strong><br><?php echo htmlspecialchars($customer['email']); ?></p>
                                    <p><strong>Role:</strong><br>
                                        <?php if(isset($customer['is_admin']) && $customer['is_admin'] == 1): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Customer</span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Member Since:</strong><br><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Total Orders</h6>
                                        <h4><?php echo $stats['order_count']; ?></h4>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Total Spent</h6>
                                        <h4>$<?php echo number_format($stats['total_spent'], 2); ?></h4>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Average Order Value</h6>
                                        <h4>$<?php echo number_format($stats['avg_order_value'], 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order History</h5>
                                </div>
                                <div class="card-body">
                                    <?php if(empty($orders)): ?>
                                        <p class="text-center py-4">No orders yet</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>Date</th>
                                                        <th>Items</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($orders as $order): ?>
                                                    <?php $items = getOrderItems($order['id']); ?>
                                                    <tr>
                                                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                        <td><?php echo count($items); ?></td>
                                                        <td><strong>$<?php echo number_format($order['total'], 2); ?></strong></td>
                                                        <td>
                                                            <span class="badge bg-<?php 
                                                                echo $order['status'] == 'completed' ? 'success' : 
                                                                    ($order['status'] == 'cancelled' ? 'danger' : 'warning'); 
                                                            ?>">
                                                                <?php echo ucfirst($order['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="admin-order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
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
    
    <script src="dist/assets/compiled/js/app.js"></script>
</body>
</html>
