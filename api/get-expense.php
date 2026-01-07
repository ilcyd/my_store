<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid expense ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT e.*, ec.name as category_name, u.name as created_by_name 
                          FROM expenses e 
                          LEFT JOIN expense_categories ec ON e.category_id = ec.id 
                          LEFT JOIN users u ON e.created_by = u.id 
                          WHERE e.id = ?");
    $stmt->execute([$id]);
    $expense = $stmt->fetch();
    
    if($expense) {
        echo json_encode([
            'success' => true,
            'expense' => $expense
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Expense not found'
        ]);
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch expense: ' . $e->getMessage()
    ]);
}
