<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

if(isset($_GET['id'])) {
    $order_id = intval($_GET['id']);
    $order = getOrderById($order_id);
    $items = getOrderItems($order_id);
    
    if(!$order) {
        header('Location: admin-orders.php');
        exit;
    }
} else {
    header('Location: admin-orders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order['id']; ?> - Admin</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="./dist/assets/extensions/sweetalert2/sweetalert2.min.css">
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
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p class="text-subtitle text-muted">Order details and management</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="admin-orders.php">Orders</a></li>
                                    <li class="breadcrumb-item active">#<?php echo $order['id']; ?></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Items</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                                 class="rounded me-3" 
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td><strong>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="row justify-content-end mt-4">
                                        <div class="col-md-6">
                                            <table class="table">
                                                <tr>
                                                    <td><strong>Subtotal:</strong></td>
                                                    <td class="text-end">₱<?php echo number_format($order['subtotal'], 2); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tax (8%):</strong></td>
                                                    <td class="text-end">₱<?php echo number_format($order['tax'], 2); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Shipping:</strong></td>
                                                    <td class="text-end">₱<?php echo number_format($order['shipping'], 2); ?></td>
                                                </tr>
                                                <tr class="table-active">
                                                    <td><strong>Total:</strong></td>
                                                    <td class="text-end"><h5 class="mb-0">₱<?php echo number_format($order['total'], 2); ?></h5></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Status</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Update Status</label>
                                        <select class="form-select" id="orderStatus">
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <button onclick="updateStatus()" class="btn btn-primary w-100">Update Status</button>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong><br><?php echo htmlspecialchars($order['shipping_name']); ?></p>
                                    <p><strong>Email:</strong><br><?php echo htmlspecialchars($order['shipping_email']); ?></p>
                                    <p><strong>Phone:</strong><br><?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Shipping Address</h5>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                        <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                        <?php echo htmlspecialchars($order['shipping_state']); ?> 
                                        <?php echo htmlspecialchars($order['shipping_zip']); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Method:</strong><br><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                                    <p><strong>Date:</strong><br><?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></p>
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
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script>
        function updateStatus() {
            const status = document.getElementById('orderStatus').value;
            
            fetch('api/admin-update-order-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: <?php echo $order['id']; ?>,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated!',
                        text: 'Order status has been updated.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update order status.'
                    });
                }
            });
        }
    </script>
</body>
</html>
