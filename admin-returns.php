<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns Management - Admin</title>
    
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
                            <h3>Returns Management</h3>
                            <p class="text-subtitle text-muted">View and manage product returns</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Returns</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">All Returns</h5>
                                <div>
                                    <select class="form-select" id="statusFilter">
                                        <option value="all">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="completed">Completed</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="returnsTable">
                                    <thead>
                                        <tr>
                                            <th>Return ID</th>
                                            <th>Type</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Refund Amount</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="returnsTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <nav class="mt-4" id="pagination"></nav>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Return Detail Modal -->
    <div class="modal fade" id="returnDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Return Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="returnDetailBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="dist/assets/static/js/components/dark.js"></script>
    <script src="dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>

    <script>
        let currentPage = 1;
        let currentStatus = 'all';

        document.getElementById('statusFilter').addEventListener('change', function() {
            currentStatus = this.value;
            currentPage = 1;
            loadReturns();
        });

        function loadReturns() {
            const tbody = document.getElementById('returnsTableBody');
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';

            fetch(`api/get-returns.php?status=${currentStatus}&limit=20&offset=${(currentPage-1)*20}`)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        displayReturns(data.returns);
                        displayPagination(data.total, 20);
                    } else {
                        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">${data.message}</td></tr>`;
                    }
                })
                .catch(error => {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading returns</td></tr>';
                });
        }

        function displayReturns(returns) {
            const tbody = document.getElementById('returnsTableBody');
            
            if(returns.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4">No returns found</td></tr>';
                return;
            }

            tbody.innerHTML = returns.map(ret => {
                const statusBadges = {
                    'pending': 'badge bg-warning',
                    'approved': 'badge bg-info',
                    'completed': 'badge bg-success',
                    'rejected': 'badge bg-danger'
                };

                return `
                    <tr>
                        <td><strong>#${ret.id}</strong></td>
                        <td><span class="badge bg-light-secondary">${ret.return_type.toUpperCase()}</span></td>
                        <td>
                            ${ret.customer_name || 'N/A'}<br>
                            <small class="text-muted">${ret.customer_email || ret.customer_phone || ''}</small>
                        </td>
                        <td>${new Date(ret.created_at).toLocaleDateString()}</td>
                        <td>${ret.items_count}</td>
                        <td><strong>₱${parseFloat(ret.total_refund).toFixed(2)}</strong></td>
                        <td>${ret.refund_method.replace('_', ' ').toUpperCase()}</td>
                        <td><span class="${statusBadges[ret.status]}">${ret.status.toUpperCase()}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="viewReturnDetail(${ret.id})">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function displayPagination(total, perPage) {
            const totalPages = Math.ceil(total / perPage);
            const pagination = document.getElementById('pagination');
            
            if(totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination justify-content-center">';
            
            // Previous
            html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage-1}); return false;">Previous</a>
            </li>`;
            
            // Pages
            for(let i = 1; i <= totalPages; i++) {
                html += `<li class="page-item ${currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>`;
            }
            
            // Next
            html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage+1}); return false;">Next</a>
            </li>`;
            
            html += '</ul>';
            pagination.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            loadReturns();
        }

        function viewReturnDetail(returnId) {
            const modal = new bootstrap.Modal(document.getElementById('returnDetailModal'));
            const body = document.getElementById('returnDetailBody');
            
            body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            modal.show();

            fetch(`api/get-return-detail.php?id=${returnId}`)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        displayReturnDetail(data.return, data.items);
                    } else {
                        body.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    body.innerHTML = '<div class="alert alert-danger">Error loading return details</div>';
                });
        }

        function displayReturnDetail(returnData, items) {
            const body = document.getElementById('returnDetailBody');
            
            const statusBadges = {
                'pending': 'badge bg-warning',
                'approved': 'badge bg-info',
                'completed': 'badge bg-success',
                'rejected': 'badge bg-danger'
            };

            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Return Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Return ID:</strong></td><td>#${returnData.id}</td></tr>
                            <tr><td><strong>Type:</strong></td><td>${returnData.return_type.toUpperCase()}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>${new Date(returnData.created_at).toLocaleString()}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="${statusBadges[returnData.status]}">${returnData.status.toUpperCase()}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Name:</strong></td><td>${returnData.customer_name || 'N/A'}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${returnData.customer_email || 'N/A'}</td></tr>
                            <tr><td><strong>Phone:</strong></td><td>${returnData.customer_phone || 'N/A'}</td></tr>
                            <tr><td><strong>Processed By:</strong></td><td>${returnData.processed_by_name || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>

                ${returnData.reason ? `
                    <div class="alert alert-light">
                        <strong>Reason:</strong> ${returnData.reason}
                    </div>
                ` : ''}

                ${returnData.notes ? `
                    <div class="alert alert-light">
                        <strong>Notes:</strong> ${returnData.notes}
                    </div>
                ` : ''}

                <h6>Returned Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Batch</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Refund</th>
                                <th>Condition</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items.map(item => `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.product_sku}</td>
                                    <td>${item.batch_number || 'N/A'}</td>
                                    <td>${item.quantity}</td>
                                    <td>₱${parseFloat(item.price).toFixed(2)}</td>
                                    <td><strong>₱${parseFloat(item.refund_amount).toFixed(2)}</strong></td>
                                    <td>${item.condition_note || 'N/A'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Total Refund:</strong></td>
                                <td colspan="2"><strong>₱${parseFloat(returnData.total_refund).toFixed(2)}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Refund Method:</strong></td>
                                <td colspan="2">${returnData.refund_method.replace('_', ' ').toUpperCase()}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

            body.innerHTML = html;
        }

        // Load returns on page load
        loadReturns();
    </script>
</body>
</html>
