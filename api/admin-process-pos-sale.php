<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if(!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Debug logging
error_log("POS Sale Data: " . print_r($data, true));

if(!isset($data['customer_name']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    
    // Create order - use shipping_name, shipping_email, shipping_phone columns
    // Use current admin's user_id for POS sales
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, shipping_name, shipping_email, shipping_phone, 
                          shipping_address, shipping_city, shipping_state, shipping_zip, 
                          payment_method, subtotal, tax, shipping, total, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', NOW())");
    
    $stmt->execute([
        $_SESSION['user_id'],  // Use logged-in admin's ID
        $data['customer_name'],
        $data['customer_email'],
        $data['customer_phone'],
        $data['shipping_address'],
        $data['shipping_city'],
        $data['shipping_state'],
        $data['shipping_zip'],
        $data['payment_method'],
        $data['subtotal'],
        $data['tax'],
        $data['shipping'],
        $data['total']
    ]);
    
    $order_id = $pdo->lastInsertId();
    
    // Add order items with batch tracking
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, batch_id) VALUES (?, ?, ?, ?, ?)");
    
    foreach($data['items'] as $item) {
        error_log("Processing item: " . print_r($item, true));
        
        // Get current product stock
        $productStmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $productStmt->execute([$item['id']]);
        $product = $productStmt->fetch();
        
        $remaining_qty = $item['quantity'];
        
        // First, deduct from product stock
        if($product && $product['stock'] > 0) {
            $deduct_from_product = min($remaining_qty, $product['stock']);
            
            // Update product stock
            $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$deduct_from_product, $item['id']]);
            
            // Add order item
            $stmt->execute([
                $order_id,
                $item['id'],
                $deduct_from_product,
                $item['price'],
                NULL
            ]);
            
            $remaining_qty -= $deduct_from_product;
        }
        
        // If more quantity needed, use FIFO batch deduction
        if($remaining_qty > 0) {
            $batch_deductions = deductFromBatches($item['id'], $remaining_qty);
            
            error_log("Batch deductions: " . print_r($batch_deductions, true));
            
            if($batch_deductions && !empty($batch_deductions)) {
                // Insert order items for each batch deduction
                foreach($batch_deductions as $deduction) {
                    $stmt->execute([
                        $order_id,
                        $item['id'],
                        $deduction['quantity'],
                        $item['price'],
                        $deduction['batch_id']
                    ]);
                }
            }
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Sale processed successfully'
    ]);
    
} catch(Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process sale: ' . $e->getMessage()
    ]);
}
