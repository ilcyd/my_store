<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 10;
$low_stock = getLowStockProducts($threshold);
$out_of_stock = getOutOfStockProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Alerts - Admin</title>
    
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
                            <h3>Stock Alerts</h3>
                            <p class="text-subtitle text-muted">Monitor low stock and out of stock products</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Stock Alerts</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <section class="section">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="text-white mb-0">Out of Stock</h5>
                                            <h2 class="text-white mb-0 mt-2"><?php echo count($out_of_stock); ?></h2>
                                        </div>
                                        <div>
                                            <i class="bi bi-x-circle" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="text-white mb-0">Low Stock (< <?php echo $threshold; ?> units)</h5>
                                            <h2 class="text-white mb-0 mt-2"><?php echo count($low_stock); ?></h2>
                                        </div>
                                        <div>
                                            <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Out of Stock Products -->
                    <?php if(count($out_of_stock) > 0): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-x-circle"></i> Out of Stock Products (<?php echo count($out_of_stock); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($out_of_stock as $product): ?>
                                        <tr class="table-danger">
                                            <td>
                                                <?php 
                                                $img_url = $product['image'] ?? 'https://via.placeholder.com/50x50?text=No+Image';
                                                ?>
                                                <img src="<?php echo htmlspecialchars($img_url); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                                     onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> 0 units
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="quickRestock(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')">
                                                    <i class="bi bi-plus-circle"></i> Restock
                                                </button>
                                                <a href="admin-products.php" class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Low Stock Products -->
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Low Stock Products (<?php echo count($low_stock); ?>)</h5>
                                <div>
                                    <label class="text-white me-2">Threshold:</label>
                                    <select class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="location.href='admin-stock-alerts.php?threshold=' + this.value">
                                        <option value="5" <?php echo $threshold == 5 ? 'selected' : ''; ?>>< 5 units</option>
                                        <option value="10" <?php echo $threshold == 10 ? 'selected' : ''; ?>>< 10 units</option>
                                        <option value="20" <?php echo $threshold == 20 ? 'selected' : ''; ?>>< 20 units</option>
                                        <option value="50" <?php echo $threshold == 50 ? 'selected' : ''; ?>>< 50 units</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if(empty($low_stock)): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i> All products have sufficient stock!
                                </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($low_stock as $product): ?>
                                        <?php 
                                        $stock_level = $product['stock'];
                                        $row_class = $stock_level <= 3 ? 'table-danger' : ($stock_level <= 5 ? 'table-warning' : '');
                                        $badge_class = $stock_level <= 3 ? 'bg-danger' : ($stock_level <= 5 ? 'bg-warning' : 'bg-warning');
                                        $img_url = $product['image'] ?? 'https://via.placeholder.com/50x50?text=No+Image';
                                        ?>
                                        <tr class="<?php echo $row_class; ?>">
                                            <td>
                                                <img src="<?php echo htmlspecialchars($img_url); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                                     onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                            <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                            <td>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <i class="bi bi-exclamation-triangle"></i> <?php echo $stock_level; ?> units
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="quickRestock(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')">
                                                    <i class="bi bi-plus-circle"></i> Restock
                                                </button>
                                                <a href="admin-products.php" class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-pencil"></i>
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
    
    <!-- Quick Restock Modal -->
    <div class="modal fade" id="restockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Restock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="restockForm">
                    <input type="hidden" id="restockProductId" name="product_id">
                    <div class="modal-body">
                        <p>Product: <strong id="restockProductName"></strong></p>
                        <div class="mb-3">
                            <label class="form-label">Add Quantity *</label>
                            <input type="number" class="form-control" name="quantity" required min="1" placeholder="e.g., 50">
                            <small class="text-muted">This will be added to current stock</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Batch Number (Optional)</label>
                            <input type="text" class="form-control" name="batch_number" placeholder="e.g., BATCH-2024-001">
                            <small class="text-muted">Leave empty if not tracking batches</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expiry Date (Optional)</label>
                            <input type="date" class="form-control" name="expiry_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script>
        function quickRestock(productId, productName) {
            document.getElementById('restockProductId').value = productId;
            document.getElementById('restockProductName').textContent = productName;
            document.getElementById('restockForm').reset();
            document.getElementById('restockProductId').value = productId;
            
            const modal = new bootstrap.Modal(document.getElementById('restockModal'));
            modal.show();
        }
        
        document.getElementById('restockForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const productId = formData.get('product_id');
            const quantity = parseInt(formData.get('quantity'));
            const batchNumber = formData.get('batch_number');
            const expiryDate = formData.get('expiry_date');
            
            if(batchNumber && batchNumber.trim() !== '') {
                // Add as batch
                fetch('api/admin-add-batch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        batch_number: batchNumber,
                        quantity: quantity,
                        expiry_date: expiryDate || null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Stock Added!',
                            text: 'Batch created and stock updated',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Failed to add stock', 'error');
                    }
                });
            } else {
                // Simple stock increase
                fetch('api/admin-quick-restock.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Stock Updated!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Failed to update stock', 'error');
                    }
                });
            }
        });
    </script>
</body>
</html>
