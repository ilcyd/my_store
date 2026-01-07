<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$stats = getDashboardStats();
$recent_orders = getAllOrders(1, 10, '');
$expiring_soon = getExpiringBatches(7);
$expired = getExpiredBatches();
$low_stock = getLowStockProducts(10);
$out_of_stock = getOutOfStockProducts();
$daily_sales = getDailySalesData(14); // Last 14 days
$daily_sales1 = getDailySalesData(1); // Last 1 days
$daily_expenses = getDailyExpensesData(14); // Last 14 days

// Calculate today's profit (revenue - expenses)
$today_sales_data = getDailySalesData(1);
$today_expenses_data = getDailyExpensesData(1);
$today_sales = isset($today_sales_data[0]) ? $today_sales_data[0]['total'] : 0;
$today_expenses = isset($today_expenses_data[0]) ? $today_expenses_data[0]['total'] : 0;
$daily_profit = $today_sales - $today_expenses;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - My Store</title>
    
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
                <h3>Admin Dashboard</h3>
            </div>
            
            <div class="page-content">
                <!-- Expiry Alerts -->
                <?php if(count($expired) > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show">
                            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Expired Batches!</h5>
                            <p><?php echo count($expired); ?> batch(es) have expired. <a href="admin-batches.php?status=expired" class="alert-link">View & manage them</a></p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(count($expiring_soon) > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible fade show">
                            <h5 class="alert-heading"><i class="bi bi-clock-history"></i> Batches Expiring Soon</h5>
                            <p><?php echo count($expiring_soon); ?> batch(es) expiring within 7 days. <a href="admin-batches.php" class="alert-link">Review batches</a></p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(count($out_of_stock) > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show">
                            <h5 class="alert-heading"><i class="bi bi-x-circle"></i> Out of Stock Products</h5>
                            <p><?php echo count($out_of_stock); ?> product(s) are out of stock. <a href="admin-stock-alerts.php" class="alert-link">Restock now</a></p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(count($low_stock) > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible fade show">
                            <h5 class="alert-heading"><i class="bi bi-exclamation-circle"></i> Low Stock Alert</h5>
                            <p><?php echo count($low_stock); ?> product(s) running low on stock (below 10 units). <a href="admin-stock-alerts.php" class="alert-link">View details</a></p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- Stats Cards -->
                        <div class="row">
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon green mb-2">
                                                    <i class="iconly-boldWallet"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Total Revenue</h6>
                                                <h6 class="font-extrabold mb-0">₱<?php echo number_format($stats['total_revenue'], 2); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon blue mb-2">
                                                    <i class="iconly-boldBuy"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Total Orders</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $stats['total_orders']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon purple mb-2">
                                                    <i class="iconly-boldProfile"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Customers</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $stats['total_customers']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon red mb-2">
                                                    <i class="iconly-boldDanger"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Pending Orders</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $stats['pending_orders']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Stats -->
                        <div class="row">
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon bg-warning mb-2">
                                                    <i class="iconly-boldBox"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Products</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $stats['total_products']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon bg-danger mb-2">
                                                    <i class="iconly-boldDanger"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Low Stock</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $stats['low_stock']; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Expenses and Profit Cards -->
                        <div class="row mt-3">
                            <div class="col-6 col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon bg-warning mb-2">
                                                    <i class="iconly-boldWallet"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Monthly Expenses</h6>
                                                <h6 class="font-extrabold mb-0">₱<?php echo number_format($stats['total_expenses'], 2); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon <?php echo $stats['net_profit'] >= 0 ? 'green' : 'bg-danger'; ?> mb-2">
                                                    <i class="iconly-boldChart"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Net Profit (This Month)</h6>
                                                <h6 class="font-extrabold mb-0">₱<?php echo number_format($stats['net_profit'], 2); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                                <div class="stats-icon <?php echo $daily_profit >= 0 ? 'green' : 'bg-danger'; ?> mb-2">
                                                    <i class="iconly-boldChart"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">Daily Profit (Today)</h6>
                                                <h6 class="font-extrabold mb-0">₱<?php echo number_format($daily_profit, 2); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Daily Sales Chart -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Daily Sales (Last 14 Days)</h4>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="salesChart" style="height: 300px;"></canvas>
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
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>Customer</th>
                                                        <th>Date</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(empty($recent_orders)): ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No orders yet</td>
                                                        </tr>
                                                    <?php else: ?>
                                                        <?php foreach($recent_orders as $order): ?>
                                                        <tr>
                                                            <td>#<?php echo $order['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($order['shipping_name']); ?></td>
                                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                            <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                                            <td>
                                                                <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                                                    <?php echo ucfirst($order['status']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="admin-order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                                    View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="admin-orders.php" class="btn btn-primary">View All Orders</a>
                                        </div>
                                    </div>
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
    <script src="dist/assets/static/js/components/dark.js"></script>
    <script src="dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="dist/assets/compiled/js/app.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Daily Sales and Expenses Chart
        const salesData = <?php echo json_encode($daily_sales); ?>;
        const expensesData = <?php echo json_encode($daily_expenses); ?>;
        
        const labels = salesData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        
        const salesTotals = salesData.map(item => item.total);
        const expensesTotals = expensesData.map(item => item.total);
        
        const ctx = document.getElementById('salesChart');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Daily Sales',
                        data: salesTotals,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Daily Expenses',
                        data: expensesTotals,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount (₱)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toFixed(0);
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
