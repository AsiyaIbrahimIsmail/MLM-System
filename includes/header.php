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
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-store"></i>
                <span>Business Loan Management</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="<?php echo $currentPage == 'index' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="about.php" class="<?php echo $currentPage == 'about' ? 'active' : ''; ?>">About</a></li>
                <li><a href="services.php" class="<?php echo $currentPage == 'services' ? 'active' : ''; ?>">Services</a></li>
                <li><a href="contact.php" class="<?php echo $currentPage == 'contact' ? 'active' : ''; ?>">Contact</a></li>
                <li><a href="login.php" class="btn btn-login">Login</a></li>
            </ul>
            <div class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>
    <?php else: ?>
    <!-- Dashboard Sidebar -->
    <div class="dashboard-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-store"></i>
                <span>BLM System</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="credit_sales.php"><i class="fas fa-shopping-cart"></i> Credit Sales</a></li>
                <li><a href="payments.php"><i class="fas fa-money-bill-wave"></i> Payments</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
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