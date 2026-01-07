<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if(!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Return ID required']);
    exit;
}

try {
    $return_id = intval($_GET['id']);
    
    // Get return details
    $stmt = $conn->prepare("
        SELECT 
            r.*,
            u.name as processed_by_name,
            u.email as processed_by_email
        FROM returns r
        LEFT JOIN users u ON r.processed_by = u.id
        WHERE r.id = ?
    ");
    $stmt->execute([$return_id]);
    $return = $stmt->fetch();
    
    if(!$return) {
        echo json_encode(['success' => false, 'message' => 'Return not found']);
        exit;
    }
    
    // Get return items
    $stmt = $conn->prepare("
        SELECT 
            ri.*,
            p.name as product_name,
            p.sku as product_sku,
            p.image as product_image,
            pb.batch_number
        FROM return_items ri
        JOIN products p ON ri.product_id = p.id
        LEFT JOIN product_batches pb ON ri.batch_id = pb.id
        WHERE ri.return_id = ?
    ");
    $stmt->execute([$return_id]);
    $items = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'return' => $return,
        'items' => $items
    ]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
