<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

// Get expenses with filters
$sql = "SELECT e.*, ec.name as category_name, u.name as created_by_name 
        FROM expenses e 
        LEFT JOIN expense_categories ec ON e.category_id = ec.id 
        LEFT JOIN users u ON e.created_by = u.id 
        WHERE e.expense_date BETWEEN ? AND ?";
$params = [$date_from, $date_to];

if($category_filter) {
    $sql .= " AND e.category_id = ?";
    $params[] = $category_filter;
}

$sql .= " ORDER BY e.expense_date DESC, e.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();

// Calculate total
$total_expenses = array_sum(array_column($expenses, 'amount'));

// Get categories
$categories = $pdo->query("SELECT * FROM expense_categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Management - My Store Admin</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
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
                            <h3>Expenses Management</h3>
                            <p class="text-subtitle text-muted">Track and manage business expenses</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Expenses</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>Expense Records</h4>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                    <i class="bi bi-plus-circle"></i> Add Expense
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <form method="GET" class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>

                            <!-- Summary -->
                            <div class="alert alert-info">
                                <h5>Total Expenses: ₱<?php echo number_format($total_expenses, 2); ?></h5>
                                <small>From <?php echo date('M d, Y', strtotime($date_from)); ?> to <?php echo date('M d, Y', strtotime($date_to)); ?></small>
                            </div>

                            <!-- Expenses Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Added By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($expenses)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No expenses found</td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach($expenses as $expense): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></td>
                                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($expense['category_name']); ?></span></td>
                                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                                <td><strong>₱<?php echo number_format($expense['amount'], 2); ?></strong></td>
                                                <td><?php echo htmlspecialchars($expense['created_by_name']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="viewExpense(<?php echo $expense['id']; ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteExpense(<?php echo $expense['id']; ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addExpenseForm">
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (₱) *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveExpense()">Save Expense</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Expense Modal -->
    <div class="modal fade" id="viewExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Expense Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="expenseDetails">
                    <!-- Details loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="dist/assets/static/js/components/dark.js"></script>
    <script src="dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    
    <script>
        function saveExpense() {
            const form = document.getElementById('addExpenseForm');
            const formData = new FormData(form);
            
            const data = {
                category_id: formData.get('category_id'),
                amount: formData.get('amount'),
                expense_date: formData.get('expense_date'),
                description: formData.get('description'),
                notes: formData.get('notes')
            };
            
            fetch('api/add-expense.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Success', 'Expense added successfully', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to add expense', 'error');
            });
        }
        
        function viewExpense(id) {
            fetch('api/get-expense.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const expense = data.expense;
                        document.getElementById('expenseDetails').innerHTML = `
                            <p><strong>Category:</strong> ${expense.category_name}</p>
                            <p><strong>Amount:</strong> ₱${parseFloat(expense.amount).toFixed(2)}</p>
                            <p><strong>Date:</strong> ${new Date(expense.expense_date).toLocaleDateString()}</p>
                            <p><strong>Description:</strong> ${expense.description}</p>
                            ${expense.notes ? `<p><strong>Notes:</strong> ${expense.notes}</p>` : ''}
                            <p><strong>Added By:</strong> ${expense.created_by_name}</p>
                            <p><strong>Added On:</strong> ${new Date(expense.created_at).toLocaleString()}</p>
                        `;
                        new bootstrap.Modal(document.getElementById('viewExpenseModal')).show();
                    }
                });
        }
        
        function deleteExpense(id) {
            Swal.fire({
                title: 'Delete Expense?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if(result.isConfirmed) {
                    fetch('api/delete-expense.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Deleted!', 'Expense has been deleted', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
