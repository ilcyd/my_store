<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true);

if(!isset($input['product_id']) || !isset($input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$product_id = (int)$input['product_id'];
$quantity = (int)$input['quantity'];

if($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be positive']);
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    $result = $stmt->execute([$quantity, $product_id]);
    
    if($result) {
        echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update stock']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
