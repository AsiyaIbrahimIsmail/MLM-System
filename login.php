<?php
/**
 * Login Page
 * Business Loan Management System
 */
$pageTitle = 'Login';
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$rememberedEmail = '';

if (isset($_COOKIE['remember_email'])) {
    $rememberedEmail = $_COOKIE['remember_email'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = $_POST['remember_me'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password'])) {
                if ($rememberMe) {
                    setcookie('remember_email', $email, time() + (86400 * 30), '/');
                } else {
                    setcookie('remember_email', '', time() - 3600, '/');
                }
                
                loginUser($user['id'], $user['full_name'], $user['email'], $user['role']);
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            $error = 'Login failed. Please try again.';
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
                radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(30, 58, 95, 0.2) 0%, transparent 40%);
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
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8a 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 30px rgba(30, 58, 95, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 10px 30px rgba(30, 58, 95, 0.3); }
            50% { box-shadow: 0 10px 40px rgba(30, 58, 95, 0.5); }
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
        
        .form-control:hover:not(:focus) {
            border-color: #d1d5db;
            background: white;
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
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle .toggle-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 4px;
            transition: color 0.3s ease;
        }
        
        .password-toggle .toggle-btn:hover {
            color: #1e3a5f;
        }
        
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1e3a5f;
            cursor: pointer;
        }
        
        .remember-me label {
            color: #4b5563;
            cursor: pointer;
            user-select: none;
        }
        
        .forgot-password {
            color: #1e3a5f;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: #2d5a8a;
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8a 100%);
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
            box-shadow: 0 4px 15px rgba(30, 58, 95, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 95, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .btn-login i {
            font-size: 1.1rem;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: #9ca3af;
            font-size: 0.85rem;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        .divider span {
            padding: 0 16px;
        }
        
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
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
        
        .demo-card {
            margin-top: 24px;
            padding: 20px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            border: 1px solid #bae6fd;
            font-size: 0.85rem;
        }
        
        .demo-card h4 {
            color: #0369a1;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        
        .demo-card p {
            color: #075985;
            margin: 4px 0;
        }
        
        .demo-card .role-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #1e3a5f;
            color: white;
            border-radius: 4px;
            font-size: 0.7rem;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-store"></i>
                </div>
                <h2>Welcome Back</h2>
                <p>Sign in to manage your business</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-icon">
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($rememberedEmail); ?>" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-icon password-toggle">
                        <input type="password" class="form-control" name="password" id="loginPassword" placeholder="Enter your password" required>
                        <i class="fas fa-lock"></i>
                        <button type="button" class="toggle-btn" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me" value="1" <?php echo $rememberedEmail ? 'checked' : ''; ?>>
                        <label for="remember_me">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="divider">
                <span>Demo Access</span>
            </div>

            <div class="demo-card">
                <h4><i class="fas fa-info-circle"></i> Test Credentials</h4>
                <p><span class="role-badge">Admin</span> admin@businessloan.com</p>
                <p><span class="role-badge">Staff</span> staff@businessloan.com</p>
                <p style="margin-top: 8px; color: #64748b;">Password: <strong>password</strong></p>
            </div>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Create one</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('loginPassword');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
