<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

requireAdmin();

if(!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Batch ID required']);
    exit;
}

$batch = getBatchById($_GET['id']);

if($batch) {
    echo json_encode(['success' => true, 'batch' => $batch]);
} else {
    echo json_encode(['success' => false, 'message' => 'Batch not found']);
}
