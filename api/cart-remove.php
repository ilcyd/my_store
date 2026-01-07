<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Cart ID is required']);
    exit;
}

$cart_id = intval($data['cart_id']);

if(removeFromCart($cart_id)) {
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart',
        'cart_count' => getCartCount()
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}
?>
