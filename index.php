<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if already logged in as admin
if(isAdmin()) {
    header('Location: admin-dashboard.php');
    exit;
}

// Auto-login as admin and redirect to admin dashboard
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM users WHERE is_admin = 1 LIMIT 1");
$stmt->execute();
$admin = $stmt->fetch();

if($admin) {
    // Set admin session
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['is_admin'] = $admin['is_admin'];
    
    // Redirect to admin dashboard
    header('Location: admin-dashboard.php');
    exit;
}

// If no admin found, show error
die('Admin user not found. Please run the database setup first.');
?>
