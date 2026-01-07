<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;

$products = getProducts($category_filter, $search, 'newest', $page, $per_page);
$total_products = getTotalProductsCount($category_filter, $search);
$total_pages = ceil($total_products / $per_page);
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    
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
                            <h3>Manage Products</h3>
                            <p class="text-subtitle text-muted">Add, edit, and manage store products</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Products</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">All Products</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                    <i class="bi bi-plus-circle"></i> Add Product
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" onchange="location.href='admin-products.php?category=' + this.value">
                                        <option value="0">All Categories</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($products)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4">No products found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach($products as $product): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                         class="rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                                <td><strong>₱<?php echo number_format($product['price'], 2); ?></strong></td>
                                                <td><?php echo $product['stock']; ?></td>
                                                <td>
                                                    <?php if($product['stock'] > 0): ?>
                                                        <span class="badge bg-success">In Stock</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editProduct(<?php echo $product['id']; ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if($total_pages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>">Previous</a>
                                    </li>
                                    <?php for($i = 1; $i <= min($total_pages, 10); $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
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
    
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProductForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" required>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" class="form-control" name="stock" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" name="sku">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Image URL</label>
                                <input type="text" class="form-control" name="image" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProductForm">
                    <input type="hidden" name="id" id="editProductId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" id="editName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" id="editCategory" required>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" name="price" id="editPrice" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" class="form-control" name="stock" id="editStock" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" name="sku" id="editSku">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Image URL</label>
                                <input type="text" class="form-control" name="image" id="editImage" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const search = this.value;
            if(search.length > 2 || search.length === 0) {
                location.href = 'admin-products.php?search=' + encodeURIComponent(search) + '&category=<?php echo $category_filter; ?>';
            }
        });
        
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch('api/admin-add-product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Added!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to add product', 'error');
                }
            });
        });
        
        function editProduct(id) {
            fetch('api/admin-get-product.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('editProductId').value = data.product.id;
                    document.getElementById('editName').value = data.product.name;
                    document.getElementById('editCategory').value = data.product.category_id;
                    document.getElementById('editPrice').value = data.product.price;
                    document.getElementById('editStock').value = data.product.stock;
                    document.getElementById('editSku').value = data.product.sku || '';
                    document.getElementById('editDescription').value = data.product.description || '';
                    document.getElementById('editImage').value = data.product.image;
                    
                    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                    modal.show();
                } else {
                    Swal.fire('Error', 'Failed to load product', 'error');
                }
            });
        }
        
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch('api/admin-update-product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Updated!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to update product', 'error');
                }
            });
        });
        
        function deleteProduct(id) {
            Swal.fire({
                title: 'Delete Product?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    fetch('api/admin-delete-product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Product Deleted!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete product', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
