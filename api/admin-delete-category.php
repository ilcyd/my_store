<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing category ID']);
    exit;
}

try {
    // Check if category has products
    $count = getCategoryProductCount($data['id']);
    if($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete category with existing products']);
        exit;
    }
    
    $result = deleteCategory(intval($data['id']));
    
    if($result) {
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
