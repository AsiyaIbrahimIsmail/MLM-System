<?php
/**
 * Dashboard Page
 * Business Loan Management System
 */
$pageTitle = 'Dashboard';
$isDashboardPage = true;
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

// Get dashboard statistics
$stats = getDashboardStats();

// Get recent customers
$stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC LIMIT 5");
$recentCustomers = $stmt->fetchAll();

// Get recent credit sales
$stmt = $pdo->query("
    SELECT cs.*, c.full_name as customer_name 
    FROM credit_sales cs 
    JOIN customers c ON cs.customer_id = c.id 
    ORDER BY cs.created_at DESC 
    LIMIT 5
");
$recentSales = $stmt->fetchAll();

// Get recent payments
$stmt = $pdo->query("
    SELECT p.*, c.full_name as customer_name 
    FROM payments p 
    JOIN customers c ON p.customer_id = c.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
$recentPayments = $stmt->fetchAll();

// Get monthly data for chart
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(sale_date, '%Y-%m') as month,
        SUM(total_amount) as total
    FROM credit_sales 
    WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(sale_date, '%Y-%m')
    ORDER BY month
");
$monthlyCredits = $stmt->fetchAll();

// Get monthly payments for chart
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(payment_date, '%Y-%m') as month,
        SUM(payment_amount) as total
    FROM payments 
    WHERE payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY month
");
$monthlyPayments = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
            <div class="stat-label">Total Customers</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo formatCurrency($stats['total_credit']); ?></div>
            <div class="stat-label">Total Credit Given</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo formatCurrency($stats['total_paid']); ?></div>
            <div class="stat-label">Total Collected</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo formatCurrency($stats['remaining_balance']); ?></div>
            <div class="stat-label">Outstanding Balance</div>
        </div>
    </div>
</div>

<div class="chart-grid">
    <div class="chart-card">
        <h3>Monthly Credit vs Payments</h3>
        <div class="chart-container">
            <canvas id="monthlyCreditChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h3>Credit Distribution</h3>
        <div class="chart-container">
            <canvas id="balanceChart"></canvas>
        </div>
    </div>
</div>

<div class="features-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 2rem;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> Recent Customers</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentCustomers)): ?>
            <div class="empty-state">
                <p>No customers yet</p>
            </div>
            <?php else: ?>
            <table class="table">
                <tbody>
                    <?php foreach ($recentCustomers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-shopping-cart"></i> Recent Sales</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentSales)): ?>
            <div class="empty-state">
                <p>No sales yet</p>
            </div>
            <?php else: ?>
            <table class="table">
                <tbody>
                    <?php foreach ($recentSales as $sale): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                        <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-money-bill-wave"></i> Recent Payments</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentPayments)): ?>
            <div class="empty-state">
                <p>No payments yet</p>
            </div>
            <?php else: ?>
            <table class="table">
                <tbody>
                    <?php foreach ($recentPayments as $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                        <td><?php echo formatCurrency($payment['payment_amount']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Pass PHP data to JavaScript
const monthlyCreditData = <?php echo json_encode($monthlyCredits); ?>;
const monthlyPaymentData = <?php echo json_encode($monthlyPayments); ?>;
</script>