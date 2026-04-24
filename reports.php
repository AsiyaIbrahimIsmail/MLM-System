<?php
/**
 * Reports Page
 * Business Loan Management System
 */
$pageTitle = 'Reports';
$isDashboardPage = true;
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

// Get filter values
$report_type = $_GET['report_type'] ?? 'summary';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Summary report - all customers with balances
$stmt = $pdo->query("
    SELECT c.id, c.full_name, c.phone, c.email,
        COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = c.id), 0) as total_credit,
        COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = c.id), 0) as total_paid
    FROM customers c
    HAVING total_credit > 0
    ORDER BY (total_credit - total_paid) DESC
");
$customerSummary = $stmt->fetchAll();

// Fully paid customers
$stmt = $pdo->query("
    SELECT c.id, c.full_name, c.phone,
        COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = c.id), 0) as total_credit,
        COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = c.id), 0) as total_paid
    FROM customers c
    HAVING total_credit > 0 AND (total_credit - total_paid) <= 0
    ORDER BY c.full_name
");
$fullyPaid = $stmt->fetchAll();

// Outstanding balances
$stmt = $pdo->query("
    SELECT c.id, c.full_name, c.phone,
        COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = c.id), 0) as total_credit,
        COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = c.id), 0) as total_paid
    FROM customers c
    HAVING total_credit > 0 AND (total_credit - total_paid) > 0
    ORDER BY (total_credit - total_paid) DESC
");
$outstanding = $stmt->fetchAll();

// Daily report
$stmt = $pdo->prepare("
    SELECT DATE(cs.sale_date) as date, COUNT(*) as transactions, SUM(cs.total_amount) as total
    FROM credit_sales cs
    WHERE cs.sale_date BETWEEN ? AND ?
    GROUP BY DATE(cs.sale_date)
    ORDER BY date DESC
");
$stmt->execute([$start_date, $end_date]);
$dailyReport = $stmt->fetchAll();

// Monthly report
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(sale_date, '%Y-%m') as month,
        COUNT(*) as transactions,
        SUM(total_amount) as total_credit
    FROM credit_sales
    GROUP BY DATE_FORMAT(sale_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
$monthlyReport = $stmt->fetchAll();

// Overall statistics
$stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM customers) as total_customers,
        (SELECT COALESCE(SUM(total_amount), 0) FROM credit_sales) as total_credit,
        (SELECT COALESCE(SUM(payment_amount), 0) FROM payments) as total_paid
");
$overallStats = $stmt->fetch();
?>
<?php include 'includes/header.php'; ?>

<div class="tabs">
    <button class="tab <?php echo $report_type == 'summary' ? 'active' : ''; ?>" onclick="location.href='?report_type=summary'">Customer Summary</button>
    <button class="tab <?php echo $report_type == 'outstanding' ? 'active' : ''; ?>" onclick="location.href='?report_type=outstanding'">Outstanding</button>
    <button class="tab <?php echo $report_type == 'paid' ? 'active' : ''; ?>" onclick="location.href='?report_type=paid'">Fully Paid</button>
    <button class="tab <?php echo $report_type == 'daily' ? 'active' : ''; ?>" onclick="location.href='?report_type=daily'">Daily Report</button>
    <button class="tab <?php echo $report_type == 'monthly' ? 'active' : ''; ?>" onclick="location.href='?report_type=monthly'">Monthly Report</button>
</div>

<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $overallStats['total_customers']; ?></div>
            <div class="stat-label">Total Customers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo formatCurrency($overallStats['total_credit']); ?></div>
            <div class="stat-label">Total Credit Issued</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo formatCurrency($overallStats['total_paid']); ?></div>
            <div class="stat-label">Total Collected</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo formatCurrency($overallStats['total_credit'] - $overallStats['total_paid']); ?></div>
            <div class="stat-label">Outstanding Balance</div>
        </div>
    </div>
</div>

<?php if ($report_type == 'summary'): ?>
<div class="search-bar">
    <h3>Customer Summary</h3>
    <button class="btn btn-primary" onclick="exportToCSV('summaryTable', 'customer_summary.csv')">
        <i class="fas fa-download"></i> Export CSV
    </button>
</div>

<div class="table-container">
    <table class="table" id="summaryTable">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Total Credit</th>
                <th>Total Paid</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customerSummary as $customer): ?>
            <?php $balance = $customer['total_credit'] - $customer['total_paid']; ?>
            <tr>
                <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                <td><?php echo formatCurrency($customer['total_credit']); ?></td>
                <td><?php echo formatCurrency($customer['total_paid']); ?></td>
                <td><?php echo formatCurrency($balance); ?></td>
                <td>
                    <span class="badge <?php echo $balance > 0 ? 'badge-warning' : 'badge-success'; ?>">
                        <?php echo $balance > 0 ? 'Outstanding' : 'Paid'; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php elseif ($report_type == 'outstanding'): ?>
<div class="search-bar">
    <h3>Customers with Outstanding Balance</h3>
    <button class="btn btn-primary" onclick="exportToCSV('outstandingTable', 'outstanding.csv')">
        <i class="fas fa-download"></i> Export CSV
    </button>
</div>

<div class="table-container">
    <table class="table" id="outstandingTable">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Total Credit</th>
                <th>Total Paid</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($outstanding as $customer): ?>
            <?php $balance = $customer['total_credit'] - $customer['total_paid']; ?>
            <tr>
                <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                <td><?php echo formatCurrency($customer['total_credit']); ?></td>
                <td><?php echo formatCurrency($customer['total_paid']); ?></td>
                <td><span class="badge badge-danger"><?php echo formatCurrency($balance); ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php elseif ($report_type == 'paid'): ?>
<div class="search-bar">
    <h3>Fully Paid Customers</h3>
    <button class="btn btn-primary" onclick="exportToCSV('paidTable', 'fully_paid.csv')">
        <i class="fas fa-download"></i> Export CSV
    </button>
</div>

<div class="table-container">
    <table class="table" id="paidTable">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Total Credit</th>
                <th>Total Paid</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fullyPaid as $customer): ?>
            <tr>
                <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                <td><?php echo formatCurrency($customer['total_credit']); ?></td>
                <td><?php echo formatCurrency($customer['total_paid']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php elseif ($report_type == 'daily'): ?>
<form method="GET" action="" class="search-bar">
    <input type="hidden" name="report_type" value="daily">
    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
    <button type="submit" class="btn btn-primary">Filter</button>
    <button type="button" class="btn btn-outline" onclick="exportToCSV('dailyTable', 'daily_report.csv')">
        <i class="fas fa-download"></i> Export CSV
    </button>
</form>

<div class="table-container">
    <table class="table" id="dailyTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Transactions</th>
                <th>Total Credit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dailyReport as $day): ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                <td><?php echo $day['transactions']; ?></td>
                <td><?php echo formatCurrency($day['total']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php elseif ($report_type == 'monthly'): ?>
<div class="search-bar">
    <h3>Monthly Report</h3>
    <button class="btn btn-primary" onclick="exportToCSV('monthlyTable', 'monthly_report.csv')">
        <i class="fas fa-download"></i> Export CSV
    </button>
</div>

<div class="table-container">
    <table class="table" id="monthlyTable">
        <thead>
            <tr>
                <th>Month</th>
                <th>Transactions</th>
                <th>Total Credit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($monthlyReport as $month): ?>
            <tr>
                <td><?php echo date('F Y', strtotime($month['month'] . '-01')); ?></td>
                <td><?php echo $month['transactions']; ?></td>
                <td><?php echo formatCurrency($month['total_credit']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="chart-grid mt-4">
    <div class="chart-card">
        <h3>Monthly Credits</h3>
        <div class="chart-container">
            <canvas id="monthlyCreditChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3>Credit Status Distribution</h3>
        <div class="chart-container">
            <canvas id="creditStatusChart"></canvas>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>