<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['cart_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Cart ID and quantity are required']);
    exit;
}

$cart_id = intval($data['cart_id']);
$quantity = intval($data['quantity']);

if($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

if(updateCartQuantity($cart_id, $quantity)) {
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated',
        'cart_count' => getCartCount()
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
}
?>
