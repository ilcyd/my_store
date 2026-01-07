<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if(!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing product ID']);
    exit;
}

$product = getProductById(intval($_GET['id']));

if($product) {
    echo json_encode(['success' => true, 'product' => $product]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}
