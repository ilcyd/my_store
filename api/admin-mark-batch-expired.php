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

$result = markBatchExpired($input['id']);

echo json_encode(['success' => $result, 'message' => $result ? 'Batch marked as expired' : 'Failed to mark batch expired']);
