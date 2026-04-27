<?php
/**
 * Forgot Password Page
 * Business Loan Management System
 */
$pageTitle = 'Forgot Password';
require_once 'includes/db.php';

$message = '';
$error = '';
$tokenSent = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
                $update->execute([$tokenHash, $expires, $user['id']]);
                
                $message = 'Password reset link has been sent to your email. (Demo mode: use your existing password)';
                $tokenSent = true;
            } else {
                $message = 'If an account exists with this email, you will receive a reset link.';
                $tokenSent = true;
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
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
    <style>
        body {
            min-height: 100vh;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f1f33 50%, #1e3a5f 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .auth-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.1) 0%, transparent 50%);
            animation: floatBg 20s ease-in-out infinite;
        }
        
        @keyframes floatBg {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(2%, 2%) rotate(1deg); }
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 36px;
        }
        
        .auth-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3); }
            50% { box-shadow: 0 10px 40px rgba(245, 158, 11, 0.5); }
        }
        
        .auth-logo i {
            font-size: 2.5rem;
            color: white;
        }
        
        .auth-header h2 {
            color: #1e3a5f;
            margin-bottom: 8px;
            font-size: 1.75rem;
        }
        
        .auth-header p {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            padding-left: 44px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #1e3a5f;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
            background: white;
            transform: translateY(-2px);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s ease;
        }
        
        .input-icon input:focus + i,
        .input-icon input:focus ~ i {
            color: #1e3a5f;
        }
        
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }
        
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            animation: slideUp 0.4s ease;
        }
        
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 24px;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .auth-footer a {
            color: #1e3a5f;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .auth-footer a:hover {
            color: #2d5a8a;
            text-decoration: underline;
        }
        
        .help-card {
            margin-top: 24px;
            padding: 20px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            border: 1px solid #fcd34d;
        }
        
        .help-card h4 {
            color: #92400e;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        
        .help-card p {
            color: #a16207;
            font-size: 0.85rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Reset Your Password</h2>
                <p>Enter your email address and we'll send you a link to reset your password</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <?php if (!$tokenSent): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-icon">
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            <?php else: ?>
            <div style="text-align: center;">
                <a href="login.php" class="btn-submit" style="text-decoration: none; display: inline-flex;">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
            <?php endif; ?>

            <div class="help-card">
                <h4><i class="fas fa-lightbulb"></i> Need Help?</h4>
                <p>In this demo version, contact the system administrator to reset your password. For production, a reset link would be sent to your email.</p>
            </div>

            <div class="auth-footer">
                <p>Remember your password? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</body>
</html>