<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

$product_id = intval($data['product_id']);
$quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;

// Check if product exists and has stock
$product = getProductById($product_id);

if(!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

if($product['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    exit;
}

// Add to cart
if(addToCart($product_id, $quantity)) {
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart',
        'cart_count' => getCartCount()
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
}
?>
