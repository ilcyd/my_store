<?php
require_once 'includes/config.php';

// Generate new password hash for admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Reset Tool</h2>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><strong>New Hash:</strong> $hash</p>";

// Update the admin password in database
try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $result = $stmt->execute([$hash, 'admin@mystore.com']);
    
    if($result) {
        echo "<p style='color: green;'><strong>✓ Success!</strong> Admin password has been updated.</p>";
        echo "<p>You can now login with:</p>";
        echo "<ul>";
        echo "<li>Email: admin@mystore.com</li>";
        echo "<li>Password: admin123</li>";
        echo "</ul>";
        echo "<p><a href='login.php'>Go to Login Page</a></p>";
    } else {
        echo "<p style='color: red;'><strong>✗ Error:</strong> Failed to update password.</p>";
    }
} catch(Exception $e) {
    echo "<p style='color: red;'><strong>✗ Error:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><small>After successful login, you can delete this file: reset-admin-password.php</small></p>";
?>
