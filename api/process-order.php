<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$required_fields = ['full_name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'country', 'payment_method'];
$order_data = [];

foreach($required_fields as $field) {
    if(!isset($_POST[$field]) || empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    $order_data[$field] = $_POST[$field];
}

$order_data['notes'] = $_POST['notes'] ?? '';

// Check if cart is not empty
$cart_items = getCartItems();
if(empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Create order
$order_id = createOrder($_SESSION['user_id'], $order_data);

if($order_id) {
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to process order']);
}
?>
