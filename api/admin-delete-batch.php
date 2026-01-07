<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

requireAdmin();

$input = json_decode(file_get_contents('php://input'), true);

if(!isset($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Batch ID required']);
    exit;
}

$result = deleteBatch($input['id']);

echo json_encode(['success' => $result, 'message' => $result ? 'Batch deleted successfully' : 'Failed to delete batch']);
