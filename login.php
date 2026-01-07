<?php
// Customer login is disabled - redirecting to admin dashboard
header('Location: index.php');
exit;
?>

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if(empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Debug: Check if user exists
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if(!$user) {
            $error = 'Email not found in database. Please check your email or register.';
        } else {
            // User exists, try to authenticate
            if(authenticateUser($email, $password)) {
                // Redirect based on user role
                if(isAdmin()) {
                    header('Location: admin-dashboard.php');
                } else {
                    header('Location: customer-dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid password. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Store</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/auth.css">
</head>

<body>
    <script src="dist/assets/static/js/initTheme.js"></script>
    <div id="auth">
        
<div class="row h-100">
    <div class="col-lg-5 col-12">
        <div id="auth-left">
            <div class="auth-logo">
                <a href="index.php"><img src="./dist/assets/compiled/svg/logo.svg" alt="Logo"></a>
            </div>
            <h1 class="auth-title">Log in.</h1>
            <p class="auth-subtitle mb-5">Log in with your data that you entered during registration.</p>

            <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="email" name="email" class="form-control form-control-xl" placeholder="Email" required>
                    <div class="form-control-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="password" name="password" class="form-control form-control-xl" placeholder="Password" required>
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
            </form>
            <div class="text-center mt-5 text-lg fs-4">
                <p class="text-gray-600">Don't have an account? <a href="register.php" class="font-bold">Sign up</a>.</p>
                <p><a class="font-bold" href="index.php">Back to Home</a></p>
            </div>

            <div class="mt-5">
                <div class="alert alert-info">
                    <strong>Demo Credentials:</strong><br>
                    <strong>Admin:</strong> admin@mystore.com / admin123<br>
                    <strong>Or</strong> create a new customer account
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 d-none d-lg-block">
        <div id="auth-right">
            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200" alt="Login" style="object-fit: cover; height: 100%; width: 100%;">
        </div>
    </div>
</div>

    </div>
</body>

</html>
