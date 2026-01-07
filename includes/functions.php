<?php
// User Functions
function getUserById($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function createUser($name, $email, $password) {
    global $pdo;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    return $stmt->execute([$name, $email, $hashed_password]);
}

function authenticateUser($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        return true;
    }
    return false;
}

// Product Functions
function getProducts($category_id = null, $search = '', $sort = 'newest', $page = 1, $per_page = 12) {
    global $pdo;
    
    $offset = ($page - 1) * $per_page;
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    $params = [];
    
    if($category_id) {
        $query .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    if($search) {
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    switch($sort) {
        case 'price_low':
            $query .= " ORDER BY p.price ASC";
            break;
        case 'price_high':
            $query .= " ORDER BY p.price DESC";
            break;
        case 'name':
            $query .= " ORDER BY p.name ASC";
            break;
        default:
            $query .= " ORDER BY p.created_at DESC";
    }
    
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getTotalProducts($category_id = null, $search = '') {
    global $pdo;
    
    $query = "SELECT COUNT(*) FROM products WHERE 1=1";
    $params = [];
    
    if($category_id) {
        $query .= " AND category_id = ?";
        $params[] = $category_id;
    }
    
    if($search) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getFeaturedProducts($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.featured = 1 AND p.stock > 0
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getRelatedProducts($category_id, $exclude_id, $limit = 4) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.category_id = ? AND p.id != ? AND p.stock > 0
                          ORDER BY RAND() 
                          LIMIT ?");
    $stmt->execute([$category_id, $exclude_id, $limit]);
    return $stmt->fetchAll();
}

// Category Functions
function getCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                        FROM categories c 
                        LEFT JOIN products p ON c.id = p.category_id 
                        GROUP BY c.id 
                        ORDER BY c.name");
    return $stmt->fetchAll();
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Cart Functions
function getCartCount() {
    if(!isset($_SESSION['user_id'])) {
        return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchColumn() ?: 0;
}

function getCartItems() {
    global $pdo;
    
    if(!isset($_SESSION['user_id'])) {
        // Guest cart
        if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return [];
        }
        
        $ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();
        
        foreach($products as &$product) {
            $product['quantity'] = $_SESSION['cart'][$product['id']];
            $product['cart_id'] = $product['id'];
        }
        
        return $products;
    }
    
    // Logged in user cart
    $stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, p.*, cat.name as category_name 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          LEFT JOIN categories cat ON p.category_id = cat.id 
                          WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchAll();
}

function getCartTotal() {
    $items = getCartItems();
    $subtotal = 0;
    
    foreach($items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $shipping = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_COST;
    $tax = $subtotal * TAX_RATE;
    $total = $subtotal + $shipping + $tax;
    
    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'tax' => $tax,
        'tax_rate' => TAX_RATE,
        'total' => $total
    ];
}

function addToCart($product_id, $quantity = 1) {
    global $pdo;
    
    if(!isset($_SESSION['user_id'])) {
        // Guest cart
        if(!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if(isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        return true;
    }
    
    // Logged in user cart
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $existing = $stmt->fetch();
    
    if($existing) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        return $stmt->execute([$quantity, $existing['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
    }
}

function updateCartQuantity($cart_id, $quantity) {
    global $pdo;
    
    if(!isset($_SESSION['user_id'])) {
        if(isset($_SESSION['cart'][$cart_id])) {
            $_SESSION['cart'][$cart_id] = $quantity;
            return true;
        }
        return false;
    }
    
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
}

function removeFromCart($cart_id) {
    global $pdo;
    
    if(!isset($_SESSION['user_id'])) {
        if(isset($_SESSION['cart'][$cart_id])) {
            unset($_SESSION['cart'][$cart_id]);
            return true;
        }
        return false;
    }
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    return $stmt->execute([$cart_id, $_SESSION['user_id']]);
}

function clearCart($user_id = null) {
    global $pdo;
    
    if(!$user_id) {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    if(!$user_id) {
        $_SESSION['cart'] = [];
        return true;
    }
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    return $stmt->execute([$user_id]);
}

// Order Functions
function createOrder($user_id, $order_data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, subtotal, tax, shipping, status, 
                              shipping_name, shipping_email, shipping_phone, shipping_address, 
                              shipping_city, shipping_state, shipping_zip, shipping_country, 
                              payment_method, notes, created_at) 
                              VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $cart_total = getCartTotal();
        
        $stmt->execute([
            $user_id,
            $cart_total['total'],
            $cart_total['subtotal'],
            $cart_total['tax'],
            $cart_total['shipping'],
            $order_data['full_name'],
            $order_data['email'],
            $order_data['phone'],
            $order_data['address'],
            $order_data['city'],
            $order_data['state'],
            $order_data['zip'],
            $order_data['country'],
            $order_data['payment_method'],
            $order_data['notes'] ?? ''
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Add order items
        $cart_items = getCartItems();
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach($cart_items as $item) {
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            
            // Update product stock
            $update_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $update_stmt->execute([$item['quantity'], $item['id']]);
        }
        
        // Clear cart
        clearCart($user_id);
        
        $pdo->commit();
        return $order_id;
        
    } catch(Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function getRecentOrders($user_id, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$user_id, $limit]);
    return $stmt->fetchAll();
}

function getOrderById($order_id, $user_id = null) {
    global $pdo;
    
    if($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
    }
    
    return $stmt->fetch();
}

function getOrderItems($order_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image, p.sku 
                          FROM order_items oi 
                          JOIN products p ON oi.product_id = p.id 
                          WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}

function getOrderStats($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT 
                          COUNT(*) as total_orders,
                          SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                          SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                          SUM(total) as total_spent
                          FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();
    
    return [
        'total_orders' => $stats['total_orders'] ?: 0,
        'pending_orders' => $stats['pending_orders'] ?: 0,
        'completed_orders' => $stats['completed_orders'] ?: 0,
        'total_spent' => $stats['total_spent'] ?: 0
    ];
}

// Admin Functions
function getAllOrders($page = 1, $per_page = 20, $status = '') {
    global $pdo;
    $offset = ($page - 1) * $per_page;
    
    $query = "SELECT * FROM orders WHERE 1=1";
    $params = [];
    
    if($status) {
        $query .= " AND status = ?";
        $params[] = $status;
    }
    
    $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getTotalOrdersCount($status = '') {
    global $pdo;
    $query = "SELECT COUNT(*) FROM orders WHERE 1=1";
    $params = [];
    
    if($status) {
        $query .= " AND status = ?";
        $params[] = $status;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function getDashboardStats() {
    global $pdo;
    
    $stats = [];
    
    // Total revenue
    $stmt = $pdo->query("SELECT SUM(total) as total_revenue FROM orders WHERE status != 'cancelled'");
    $stats['total_revenue'] = $stmt->fetchColumn() ?: 0;
    
    // Total expenses (current month)
    $stmt = $pdo->query("SELECT SUM(amount) as total_expenses FROM expenses WHERE MONTH(expense_date) = MONTH(CURDATE()) AND YEAR(expense_date) = YEAR(CURDATE())");
    $stats['total_expenses'] = $stmt->fetchColumn() ?: 0;
    
    // Net profit (revenue - expenses)
    $stmt = $pdo->query("SELECT SUM(total) as monthly_revenue FROM orders WHERE status != 'cancelled' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    $monthly_revenue = $stmt->fetchColumn() ?: 0;
    $stats['net_profit'] = $monthly_revenue - $stats['total_expenses'];
    
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $stats['total_orders'] = $stmt->fetchColumn();
    
    // Total customers
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0");
    $stats['total_customers'] = $stmt->fetchColumn();
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $stats['total_products'] = $stmt->fetchColumn();
    
    // Pending orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetchColumn();
    
    // Low stock products (configurable threshold, default 10)
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock > 0 AND stock < 10");
    $stats['low_stock'] = $stmt->fetchColumn();
    
    // Out of stock products
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock = 0");
    $stats['out_of_stock'] = $stmt->fetchColumn();
    
    return $stats;
}

// Get daily sales data for chart (last 14 days)
function getDailySalesData($days = 14) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            DATE(created_at) as sale_date,
            SUM(total) as daily_total,
            COUNT(*) as order_count
        FROM orders 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        AND status != 'cancelled'
        GROUP BY DATE(created_at)
        ORDER BY sale_date ASC
    ");
    
    $stmt->execute([$days]);
    $results = $stmt->fetchAll();
    
    // Fill in missing dates with 0
    $data = [];
    for($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $found = false;
        
        foreach($results as $row) {
            if($row['sale_date'] === $date) {
                $data[] = [
                    'date' => $date,
                    'total' => (float)$row['daily_total'],
                    'orders' => (int)$row['order_count']
                ];
                $found = true;
                break;
            }
        }
        
        if(!$found) {
            $data[] = [
                'date' => $date,
                'total' => 0,
                'orders' => 0
            ];
        }
    }
    
    return $data;
}

// Get daily expenses data for chart (last 14 days)
function getDailyExpensesData($days = 14) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            DATE(expense_date) as expense_date,
            SUM(amount) as daily_total
        FROM expenses 
        WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY DATE(expense_date)
        ORDER BY expense_date ASC
    ");
    
    $stmt->execute([$days]);
    $results = $stmt->fetchAll();
    
    // Fill in missing dates with 0
    $data = [];
    for($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $found = false;
        
        foreach($results as $row) {
            if($row['expense_date'] === $date) {
                $data[] = [
                    'date' => $date,
                    'total' => (float)$row['daily_total']
                ];
                $found = true;
                break;
            }
        }
        
        if(!$found) {
            $data[] = [
                'date' => $date,
                'total' => 0
            ];
        }
    }
    
    return $data;
}

function getLowStockProducts($threshold = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.stock > 0 AND p.stock < ? 
                          ORDER BY p.stock ASC");
    $stmt->execute([$threshold]);
    return $stmt->fetchAll();
}

function getOutOfStockProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, c.name as category_name 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.stock = 0 
                        ORDER BY p.name ASC");
    return $stmt->fetchAll();
}

function updateOrderStatus($order_id, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $order_id]);
}

function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name, 
               COALESCE(SUM(oi.quantity), 0) as total_sold
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN order_items oi ON p.id = oi.product_id
        GROUP BY p.id
        ORDER BY total_sold DESC, p.name ASC
    ");
    return $stmt->fetchAll();
}

// Get products with available stock for POS
function getProductsForPOS() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name,
               COALESCE(SUM(DISTINCT pb.quantity), 0) as batch_stock,
               COALESCE(SUM(DISTINCT oi.quantity), 0) as total_sold
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_batches pb ON p.id = pb.product_id 
            AND pb.status = 'active'
            AND (pb.expiry_date IS NULL OR pb.expiry_date >= CURDATE())
        LEFT JOIN order_items oi ON p.id = oi.product_id
        WHERE p.stock > 0
        GROUP BY p.id
        ORDER BY total_sold DESC, p.name ASC
    ");
    return $stmt->fetchAll();
}

function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    
    return $classes[$status] ?? 'secondary';
}

function getTotalProductsCount($category_id = 0, $search = '') {
    global $pdo;
    $query = "SELECT COUNT(*) FROM products WHERE 1=1";
    $params = [];
    
    if($category_id) {
        $query .= " AND category_id = ?";
        $params[] = $category_id;
    }
    
    if($search) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function addProduct($data) {
    global $pdo;
    
    // Generate unique slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name'])));
    
    // Ensure slug is unique by appending timestamp if needed
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ?");
    $checkStmt->execute([$slug]);
    if($checkStmt->fetchColumn() > 0) {
        $slug .= '-' . time();
    }
    
    // Generate SKU if not provided
    $sku = $data['sku'] ?? 'SKU-' . strtoupper(substr(uniqid(), -8));
    
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, stock, category_id, image, sku) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['name'],
        $slug,
        $data['description'] ?? '',
        $data['price'],
        $data['stock'],
        $data['category_id'],
        $data['image'] ?? '',
        $sku
    ]);
}

function updateProduct($data) {
    global $pdo;
    
    // Generate slug from name if updating
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name'])));
    
    // Ensure slug is unique (excluding current product)
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ? AND id != ?");
    $checkStmt->execute([$slug, $data['id']]);
    if($checkStmt->fetchColumn() > 0) {
        $slug .= '-' . time();
    }
    
    $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ?, sku = ? WHERE id = ?");
    return $stmt->execute([
        $data['name'],
        $slug,
        $data['description'] ?? '',
        $data['price'],
        $data['stock'],
        $data['category_id'],
        $data['image'] ?? '',
        $data['sku'] ?? 'SKU-' . strtoupper(substr(uniqid(), -8)),
        $data['id']
    ]);
}

function deleteProduct($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$product_id]);
}

function getCategoryProductCount($category_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetchColumn();
}

function addCategory($name, $description) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    return $stmt->execute([$name, $description]);
}

function updateCategory($id, $name, $description) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    return $stmt->execute([$name, $description, $id]);
}

function deleteCategory($category_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$category_id]);
}

function getAllCustomers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function getCustomerStats($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) as order_count, COALESCE(SUM(total), 0) as total_spent, COALESCE(AVG(total), 0) as avg_order_value FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

function getUserOrders($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Batch Management Functions
function addProductBatch($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO product_batches (product_id, batch_number, expiry_date, quantity, cost_price, manufacture_date, supplier, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $data['product_id'],
        $data['batch_number'],
        $data['expiry_date'] ?? null,
        $data['quantity'],
        $data['cost_price'] ?? null,
        $data['manufacture_date'] ?? null,
        $data['supplier'] ?? null,
        $data['notes'] ?? null
    ]);
    
    if($result) {
        // Update total product stock
        $updateStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $updateStock->execute([$data['quantity'], $data['product_id']]);
    }
    
    return $result;
}

function getProductBatches($product_id, $active_only = true) {
    global $pdo;
    $query = "SELECT * FROM product_batches WHERE product_id = ?";
    $params = [$product_id];
    
    if($active_only) {
        $query .= " AND status = 'active' AND quantity > 0";
    }
    
    $query .= " ORDER BY expiry_date ASC, received_date ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getBatchById($batch_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT pb.*, p.name as product_name FROM product_batches pb 
                          JOIN products p ON pb.product_id = p.id 
                          WHERE pb.id = ?");
    $stmt->execute([$batch_id]);
    return $stmt->fetch();
}

function updateBatchQuantity($batch_id, $quantity_change) {
    global $pdo;
    $batch = getBatchById($batch_id);
    
    if(!$batch) return false;
    
    $new_quantity = $batch['quantity'] + $quantity_change;
    if($new_quantity < 0) return false;
    
    // Update batch quantity
    $stmt = $pdo->prepare("UPDATE product_batches SET quantity = ? WHERE id = ?");
    $result = $stmt->execute([$new_quantity, $batch_id]);
    
    if($result) {
        // Update product total stock
        $updateStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $updateStock->execute([$quantity_change, $batch['product_id']]);
    }
    
    return $result;
}

function getOldestBatch($product_id, $quantity_needed) {
    global $pdo;
    // Get oldest non-expired batch with FIFO logic
    $stmt = $pdo->prepare("SELECT * FROM product_batches 
                          WHERE product_id = ? 
                          AND status = 'active' 
                          AND quantity > 0 
                          AND (expiry_date IS NULL OR expiry_date >= CURDATE())
                          ORDER BY expiry_date ASC, received_date ASC 
                          LIMIT 1");
    $stmt->execute([$product_id]);
    $batch = $stmt->fetch();
    
    if($batch && $batch['quantity'] >= $quantity_needed) {
        return $batch;
    }
    
    return $batch; // Return even if not enough, caller will handle
}

function deductFromBatches($product_id, $quantity) {
    global $pdo;
    $remaining = $quantity;
    $deductions = [];
    
    while($remaining > 0) {
        $batch = getOldestBatch($product_id, $remaining);
        
        if(!$batch) {
            // No more batches available
            break;
        }
        
        $deduct_qty = min($remaining, $batch['quantity']);
        
        if(updateBatchQuantity($batch['id'], -$deduct_qty)) {
            $deductions[] = [
                'batch_id' => $batch['id'],
                'quantity' => $deduct_qty
            ];
            $remaining -= $deduct_qty;
        } else {
            break;
        }
    }
    
    return $deductions;
}

function getExpiringBatches($days = 7) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT pb.*, p.name as product_name, p.sku 
                          FROM product_batches pb 
                          JOIN products p ON pb.product_id = p.id 
                          WHERE pb.status = 'active' 
                          AND pb.quantity > 0 
                          AND pb.expiry_date IS NOT NULL 
                          AND pb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                          ORDER BY pb.expiry_date ASC");
    $stmt->execute([$days]);
    return $stmt->fetchAll();
}

function getExpiredBatches() {
    global $pdo;
    $stmt = $pdo->query("SELECT pb.*, p.name as product_name, p.sku 
                        FROM product_batches pb 
                        JOIN products p ON pb.product_id = p.id 
                        WHERE pb.status = 'active' 
                        AND pb.quantity > 0 
                        AND pb.expiry_date < CURDATE()
                        ORDER BY pb.expiry_date ASC");
    return $stmt->fetchAll();
}

function markBatchExpired($batch_id) {
    global $pdo;
    $batch = getBatchById($batch_id);
    
    if($batch && $batch['quantity'] > 0) {
        // Mark as expired
        $stmt = $pdo->prepare("UPDATE product_batches SET status = 'expired' WHERE id = ?");
        $result = $stmt->execute([$batch_id]);
        
        if($result) {
            // Deduct from product stock
            $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$batch['quantity'], $batch['product_id']]);
        }
        
        return $result;
    }
    
    return false;
}

function updateBatch($data) {
    global $pdo;
    $old_batch = getBatchById($data['id']);
    
    if(!$old_batch) return false;
    
    $quantity_diff = $data['quantity'] - $old_batch['quantity'];
    
    $stmt = $pdo->prepare("UPDATE product_batches 
                          SET batch_number = ?, expiry_date = ?, quantity = ?, 
                              cost_price = ?, manufacture_date = ?, supplier = ?, notes = ?
                          WHERE id = ?");
    $result = $stmt->execute([
        $data['batch_number'],
        $data['expiry_date'] ?? null,
        $data['quantity'],
        $data['cost_price'] ?? null,
        $data['manufacture_date'] ?? null,
        $data['supplier'] ?? null,
        $data['notes'] ?? null,
        $data['id']
    ]);
    
    if($result && $quantity_diff != 0) {
        // Update product stock
        $updateStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $updateStock->execute([$quantity_diff, $old_batch['product_id']]);
    }
    
    return $result;
}

function deleteBatch($batch_id) {
    global $pdo;
    $batch = getBatchById($batch_id);
    
    if($batch) {
        // Deduct from product stock
        $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $updateStock->execute([$batch['quantity'], $batch['product_id']]);
        
        // Delete batch
        $stmt = $pdo->prepare("DELETE FROM product_batches WHERE id = ?");
        return $stmt->execute([$batch_id]);
    }
    
    return false;
}

function getAllBatches($status = null) {
    global $pdo;
    $query = "SELECT pb.*, p.name as product_name, p.sku 
              FROM product_batches pb 
              JOIN products p ON pb.product_id = p.id";
    $params = [];
    
    if($status) {
        $query .= " WHERE pb.status = ?";
        $params[] = $status;
    }
    
    $query .= " ORDER BY pb.expiry_date ASC, pb.received_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function isProductExpired($product_id) {
    global $pdo;
    // Check if all batches are expired
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_batches 
                          WHERE product_id = ? 
                          AND status = 'active' 
                          AND quantity > 0 
                          AND (expiry_date IS NULL OR expiry_date >= CURDATE())");
    $stmt->execute([$product_id]);
    return $stmt->fetchColumn() == 0;
}
?>
