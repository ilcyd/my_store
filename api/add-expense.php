<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['category_id']) || !isset($data['amount']) || !isset($data['expense_date']) || !isset($data['description'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO expenses (category_id, amount, description, expense_date, notes, created_by, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([
        $data['category_id'],
        $data['amount'],
        $data['description'],
        $data['expense_date'],
        $data['notes'] ?? null,
        $_SESSION['user_id']
    ]);
    
    echo json_encode([
        'success' => true,
        'expense_id' => $pdo->lastInsertId(),
        'message' => 'Expense added successfully'
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add expense: ' . $e->getMessage()
    ]);
}
