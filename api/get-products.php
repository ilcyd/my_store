<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $sql = "SELECT p.*, c.name as category_name,
                   COALESCE(SUM(pb.quantity), 0) as batch_stock
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_batches pb ON p.id = pb.product_id 
                AND pb.status = 'active'
                AND (pb.expiry_date IS NULL OR pb.expiry_date >= CURDATE())
            WHERE p.stock > 0";
    
    $params = [];
    
    if($category_id) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    if($search) {
        $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " GROUP BY p.id HAVING batch_stock > 0 ORDER BY p.name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch products: ' . $e->getMessage()
    ]);
}
