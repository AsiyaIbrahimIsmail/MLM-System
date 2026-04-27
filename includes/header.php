<?php
/**
 * Header Include File
 * Business Loan Management System
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Business Loan Management' : 'Business Loan Management System'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
    </div>

    <?php if (!isset($isDashboardPage)): ?>
    <!-- Public Navigation -->
    <nav class="navbar">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <a href="index.php" class="nav-brand" style="display: flex; align-items: center; gap: 10px; font-size: 1.25rem; font-weight: 700; color: var(--primary-color); text-decoration: none;">
                <i class="fas fa-store"></i>
                <span>Business Loan Management</span>
            </a>
            <ul class="nav-menu" id="navMenu" style="display: flex; align-items: center; gap: 20px; list-style: none; margin: 0; padding: 0;">
                <li><a href="index.php" class="<?php echo $currentPage == 'index' ? 'active' : ''; ?>" style="font-weight: 500; color: #4b5563; padding: 8px 16px; border-radius: 8px; text-decoration: none;">Home</a></li>
                <li><a href="about.php" class="<?php echo $currentPage == 'about' ? 'active' : ''; ?>" style="font-weight: 500; color: #4b5563; padding: 8px 16px; border-radius: 8px; text-decoration: none;">About</a></li>
                <li><a href="services.php" class="<?php echo $currentPage == 'services' ? 'active' : ''; ?>" style="font-weight: 500; color: #4b5563; padding: 8px 16px; border-radius: 8px; text-decoration: none;">Services</a></li>
                <li><a href="contact.php" class="<?php echo $currentPage == 'contact' ? 'active' : ''; ?>" style="font-weight: 500; color: #4b5563; padding: 8px 16px; border-radius: 8px; text-decoration: none;">Contact</a></li>
                <li><a href="login.php" class="btn btn-login" style="padding: 8px 20px; background: #10b981; color: white; border-radius: 8px; text-decoration: none;">Login</a></li>
            </ul>
            <div class="nav-toggle" id="navToggle" onclick="toggleNav()" style="display: none; font-size: 1.5rem; cursor: pointer; padding: 8px;">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>
    <div style="height: 70px;"></div>
    
    <script>
    function toggleNav() {
        var navMenu = document.getElementById('navMenu');
        var navToggle = document.getElementById('navToggle');
        navMenu.classList.toggle('active');
        navToggle.classList.toggle('active');
    }
    </script>
    <?php else: ?>
    <!-- Dashboard Sidebar -->
    <div class="dashboard-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-store"></i>
                <span>BLM System</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="<?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="customers.php" class="<?php echo $currentPage == 'customers' ? 'active' : ''; ?>"><i class="fas fa-users"></i> <span>Customers</span></a></li>
                <li><a href="products.php" class="<?php echo $currentPage == 'products' ? 'active' : ''; ?>"><i class="fas fa-box"></i> <span>Products</span></a></li>
                <li><a href="credit_sales.php" class="<?php echo $currentPage == 'credit_sales' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> <span>Credit Sales</span></a></li>
                <li><a href="payments.php" class="<?php echo $currentPage == 'payments' ? 'active' : ''; ?>"><i class="fas fa-money-bill-wave"></i> <span>Payments</span></a></li>
                <li><a href="reports.php" class="<?php echo $currentPage == 'reports' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
                <li class="divider"></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Main Site</a></li>
                <li><a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="dashboard-header">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
                <div class="user-info">
                    <span><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>
    <?php endif; ?>
