<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true);

if(!isset($input['product_id']) || !isset($input['batch_number']) || !isset($input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$result = addProductBatch($input);

echo json_encode(['success' => $result, 'message' => $result ? 'Batch added successfully' : 'Failed to add batch']);
