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

if(!isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'No items provided']);
    exit;
}

try {
    $conn->beginTransaction();
    
    // Calculate total refund
    $total_refund = 0;
    foreach($data['items'] as $item) {
        $total_refund += floatval($item['price']) * intval($item['quantity']);
    }
    
    // Create return record
    $stmt = $conn->prepare("INSERT INTO returns (
        order_id, return_type, total_refund, refund_method, reason, 
        status, processed_by, customer_name, customer_email, customer_phone, notes
    ) VALUES (?, ?, ?, ?, ?, 'completed', ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['order_id'] ?? null,
        $data['return_type'] ?? 'pos',
        $total_refund,
        $data['refund_method'] ?? 'cash',
        $data['reason'] ?? '',
        $_SESSION['user_id'],
        $data['customer_name'] ?? '',
        $data['customer_email'] ?? '',
        $data['customer_phone'] ?? '',
        $data['notes'] ?? ''
    ]);
    
    $return_id = $conn->lastInsertId();
    
    // Process each returned item
    foreach($data['items'] as $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);
        $refund_amount = $price * $quantity;
        
        // Insert return item
        $stmt = $conn->prepare("INSERT INTO return_items (
            return_id, product_id, batch_id, quantity, price, refund_amount, condition_note
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $return_id,
            $product_id,
            $item['batch_id'] ?? null,
            $quantity,
            $price,
            $refund_amount,
            $item['condition'] ?? 'Good'
        ]);
        
        // Restore stock to product
        $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$quantity, $product_id]);
        
        // If batch_id provided, restore to specific batch
        if(isset($item['batch_id']) && $item['batch_id']) {
            $batch_id = intval($item['batch_id']);
            $stmt = $conn->prepare("UPDATE product_batches SET quantity = quantity + ? WHERE id = ?");
            $stmt->execute([$quantity, $batch_id]);
        } else {
            // Find the most recent active batch to add stock to
            $stmt = $conn->prepare("
                SELECT id FROM product_batches 
                WHERE product_id = ? AND status = 'active' 
                ORDER BY received_date DESC LIMIT 1
            ");
            $stmt->execute([$product_id]);
            $batch = $stmt->fetch();
            
            if($batch) {
                $stmt = $conn->prepare("UPDATE product_batches SET quantity = quantity + ? WHERE id = ?");
                $stmt->execute([$quantity, $batch['id']]);
            }
        }
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'return_id' => $return_id,
        'total_refund' => $total_refund,
        'message' => 'Return processed successfully'
    ]);
    
} catch(Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
