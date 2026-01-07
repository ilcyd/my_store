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
    // Get filter parameters
    $status = $_GET['status'] ?? 'all';
    $limit = intval($_GET['limit'] ?? 50);
    $offset = intval($_GET['offset'] ?? 0);
    
    // Build query
    $where = "WHERE 1=1";
    $params = [];
    
    if($status !== 'all') {
        $where .= " AND r.status = ?";
        $params[] = $status;
    }
    
    // Get returns with user info
    $stmt = $conn->prepare("
        SELECT 
            r.*,
            u.name as processed_by_name,
            o.id as order_number,
            COUNT(ri.id) as items_count
        FROM returns r
        LEFT JOIN users u ON r.processed_by = u.id
        LEFT JOIN orders o ON r.order_id = o.id
        LEFT JOIN return_items ri ON r.id = ri.return_id
        $where
        GROUP BY r.id
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $returns = $stmt->fetchAll();
    
    // Get total count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM returns r $where");
    $stmt->execute(array_slice($params, 0, -2)); // Remove limit and offset
    $total = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'returns' => $returns,
        'total' => $total
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
