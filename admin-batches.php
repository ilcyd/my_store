<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'active';
$batches = getAllBatches($status_filter);
$expiring_soon = getExpiringBatches(7);
$expired = getExpiredBatches();
$all_products = getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Management - Admin</title>
    
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
                            <h3>Batch & Expiry Management</h3>
                            <p class="text-subtitle text-muted">Track product batches and expiry dates</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Batches</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Alert Cards -->
                <section class="section">
                    <div class="row">
                        <?php if(count($expired) > 0): ?>
                        <div class="col-12 mb-3">
                            <div class="alert alert-danger">
                                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Expired Batches</h5>
                                <p class="mb-0"><?php echo count($expired); ?> batch(es) have expired and need attention!</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(count($expiring_soon) > 0): ?>
                        <div class="col-12 mb-3">
                            <div class="alert alert-warning">
                                <h5 class="alert-heading"><i class="bi bi-clock"></i> Expiring Soon</h5>
                                <p class="mb-0"><?php echo count($expiring_soon); ?> batch(es) expiring within 7 days.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">All Batches</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBatchModal">
                                    <i class="bi bi-plus-circle"></i> Add Batch
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <select class="form-select" onchange="location.href='admin-batches.php?status=' + this.value">
                                    <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active Batches</option>
                                    <option value="expired" <?php echo $status_filter == 'expired' ? 'selected' : ''; ?>>Expired Batches</option>
                                    <option value="" <?php echo $status_filter == '' ? 'selected' : ''; ?>>All Batches</option>
                                </select>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Batch #</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Expiry Date</th>
                                            <th>Status</th>
                                            <th>Received</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($batches)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4">No batches found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach($batches as $batch): ?>
                                            <?php 
                                            $is_expired = $batch['expiry_date'] && strtotime($batch['expiry_date']) < time();
                                            $days_until_expiry = $batch['expiry_date'] ? ceil((strtotime($batch['expiry_date']) - time()) / 86400) : null;
                                            $is_expiring_soon = $days_until_expiry !== null && $days_until_expiry <= 7 && $days_until_expiry >= 0;
                                            ?>
                                            <tr class="<?php echo $is_expired ? 'table-danger' : ($is_expiring_soon ? 'table-warning' : ''); ?>">
                                                <td><strong><?php echo htmlspecialchars($batch['batch_number']); ?></strong></td>
                                                <td>
                                                    <?php echo htmlspecialchars($batch['product_name']); ?><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($batch['sku']); ?></small>
                                                </td>
                                                <td><?php echo $batch['quantity']; ?> units</td>
                                                <td>
                                                    <?php if($batch['expiry_date']): ?>
                                                        <?php echo date('M d, Y', strtotime($batch['expiry_date'])); ?>
                                                        <?php if($days_until_expiry !== null): ?>
                                                            <br><small class="<?php echo $is_expired ? 'text-danger' : ($is_expiring_soon ? 'text-warning' : 'text-muted'); ?>">
                                                                <?php 
                                                                if($is_expired) {
                                                                    echo 'Expired ' . abs($days_until_expiry) . ' days ago';
                                                                } else if($days_until_expiry == 0) {
                                                                    echo 'Expires today!';
                                                                } else {
                                                                    echo $days_until_expiry . ' days left';
                                                                }
                                                                ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No expiry</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($batch['status'] == 'expired'): ?>
                                                        <span class="badge bg-danger">Expired</span>
                                                    <?php elseif($is_expired): ?>
                                                        <span class="badge bg-danger">Past Expiry</span>
                                                    <?php elseif($is_expiring_soon): ?>
                                                        <span class="badge bg-warning">Expiring Soon</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($batch['received_date'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="viewBatch(<?php echo $batch['id']; ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" onclick="editBatch(<?php echo $batch['id']; ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <?php if($is_expired && $batch['status'] == 'active'): ?>
                                                    <button class="btn btn-sm btn-danger" onclick="markExpired(<?php echo $batch['id']; ?>)">
                                                        <i class="bi bi-x-circle"></i> Expire
                                                    </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
    
    <!-- Add Batch Modal -->
    <div class="modal fade" id="addBatchModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addBatchForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product *</label>
                                <select class="form-select" name="product_id" required>
                                    <option value="">Select Product</option>
                                    <?php foreach($all_products as $prod): ?>
                                    <option value="<?php echo $prod['id']; ?>"><?php echo htmlspecialchars($prod['name']); ?> (<?php echo $prod['sku']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch Number *</label>
                                <input type="text" class="form-control" name="batch_number" required placeholder="e.g., BATCH-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantity" required min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Price</label>
                                <input type="number" step="0.01" class="form-control" name="cost_price" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manufacture Date</label>
                                <input type="date" class="form-control" name="manufacture_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" class="form-control" name="supplier" placeholder="Optional">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Batch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View Batch Modal -->
    <div class="modal fade" id="viewBatchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Batch Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="batchDetails">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Batch Modal -->
    <div class="modal fade" id="editBatchModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBatchForm">
                    <input type="hidden" name="id" id="editBatchId">
                    <input type="hidden" name="product_id" id="editProductId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Product</label>
                                <input type="text" class="form-control" id="editProductName" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch Number *</label>
                                <input type="text" class="form-control" name="batch_number" id="editBatchNumber" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantity" id="editQuantity" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Price</label>
                                <input type="number" step="0.01" class="form-control" name="cost_price" id="editCostPrice">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manufacture Date</label>
                                <input type="date" class="form-control" name="manufacture_date" id="editManufactureDate">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date" id="editExpiryDate">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" class="form-control" name="supplier" id="editSupplier">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" id="editNotes" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="confirmDeleteBatch()">Delete Batch</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Batch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script>
        document.getElementById('addBatchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch('api/admin-add-batch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Batch Added!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to add batch', 'error');
                }
            });
        });
        
        function viewBatch(id) {
            fetch('api/admin-get-batch.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const batch = data.batch;
                    document.getElementById('batchDetails').innerHTML = `
                        <p><strong>Product:</strong> ${batch.product_name} (${batch.sku})</p>
                        <p><strong>Batch Number:</strong> ${batch.batch_number}</p>
                        <p><strong>Quantity:</strong> ${batch.quantity} units</p>
                        <p><strong>Cost Price:</strong> ${batch.cost_price ? '₱' + parseFloat(batch.cost_price).toFixed(2) : 'N/A'}</p>
                        <p><strong>Manufacture Date:</strong> ${batch.manufacture_date || 'N/A'}</p>
                        <p><strong>Expiry Date:</strong> ${batch.expiry_date || 'No expiry'}</p>
                        <p><strong>Received:</strong> ${new Date(batch.received_date).toLocaleString()}</p>
                        <p><strong>Supplier:</strong> ${batch.supplier || 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${batch.status == 'active' ? 'success' : 'danger'}">${batch.status}</span></p>
                        ${batch.notes ? `<p><strong>Notes:</strong> ${batch.notes}</p>` : ''}
                    `;
                    const modal = new bootstrap.Modal(document.getElementById('viewBatchModal'));
                    modal.show();
                }
            });
        }
        
        function editBatch(id) {
            fetch('api/admin-get-batch.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const batch = data.batch;
                    document.getElementById('editBatchId').value = batch.id;
                    document.getElementById('editProductId').value = batch.product_id;
                    document.getElementById('editProductName').value = batch.product_name + ' (' + batch.sku + ')';
                    document.getElementById('editBatchNumber').value = batch.batch_number;
                    document.getElementById('editQuantity').value = batch.quantity;
                    document.getElementById('editCostPrice').value = batch.cost_price || '';
                    document.getElementById('editManufactureDate').value = batch.manufacture_date || '';
                    document.getElementById('editExpiryDate').value = batch.expiry_date || '';
                    document.getElementById('editSupplier').value = batch.supplier || '';
                    document.getElementById('editNotes').value = batch.notes || '';
                    
                    const modal = new bootstrap.Modal(document.getElementById('editBatchModal'));
                    modal.show();
                }
            });
        }
        
        document.getElementById('editBatchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch('api/admin-update-batch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Batch Updated!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to update batch', 'error');
                }
            });
        });
        
        function confirmDeleteBatch() {
            const batchId = document.getElementById('editBatchId').value;
            Swal.fire({
                title: 'Delete Batch?',
                text: 'This will remove the batch and adjust product stock!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    fetch('api/admin-delete-batch.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: batchId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Batch Deleted!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete batch', 'error');
                        }
                    });
                }
            });
        }
        
        function markExpired(batchId) {
            Swal.fire({
                title: 'Mark Batch as Expired?',
                text: 'This will remove the batch quantity from available stock.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, mark expired',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    fetch('api/admin-mark-batch-expired.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: batchId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Batch Marked Expired!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to mark batch as expired', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
