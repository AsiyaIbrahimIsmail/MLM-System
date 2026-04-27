<?php
/**
 * Database Connection File
 * Business Loan Management System
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'business_loan_management');

try {
    // Create database connection using PDO
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Sanitize input to prevent SQL injection
 */
function sanitize($input) {
    global $pdo;
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return "$" . number_format($amount, 2);
}

/**
 * Calculate remaining balance for a customer
 */
function getCustomerBalance($customerId) {
    global $pdo;

    // Get total credit
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM credit_sales WHERE customer_id = ?");
    $stmt->execute([$customerId]);
    $totalCredit = $stmt->fetch()['total'];

    // Get total paid
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(payment_amount), 0) as total FROM payments WHERE customer_id = ?");
    $stmt->execute([$customerId]);
    $totalPaid = $stmt->fetch()['total'];

    return $totalCredit - $totalPaid;
}

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    global $pdo;

    $stats = [];

    // Total customers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM customers");
    $stats['total_customers'] = $stmt->fetch()['count'];

    // Total credit given
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM credit_sales");
    $stats['total_credit'] = $stmt->fetch()['total'];

    // Total paid
    $stmt = $pdo->query("SELECT COALESCE(SUM(payment_amount), 0) as total FROM payments");
    $stats['total_paid'] = $stmt->fetch()['total'];

    // Remaining balance
    $stats['remaining_balance'] = $stats['total_credit'] - $stats['total_paid'];

    return $stats;
}
