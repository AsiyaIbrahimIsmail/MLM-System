<?php
/**
 * Register Page
 * Business Loan Management System
 */
$pageTitle = 'Register';
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        try {
            $hashed_password = hashPassword($password);

            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'staff')");
            $stmt->execute([$full_name, $email, $hashed_password]);

            $success = 'Registration successful! You can now login.';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Email already registered';
            } else {
                $error = 'Registration failed. Please try again.';
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
    <title><?php echo $pageTitle; ?> - Business Loan Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-store" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h2>Create Account</h2>
                <p>Register for a new account</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="full_name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
