<?php
/**
 * Customer Profile Page
 * Business Loan Management System
 */
$pageTitle = 'Customer Profile';
$isDashboardPage = true;
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$customer_id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$customer_id) {
    header('Location: customers.php');
    exit();
}

// Get customer details
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    header('Location: customers.php');
    exit();
}

// Get customer balance
$stmt = $pdo->prepare("
    SELECT 
        COALESCE((SELECT SUM(total_amount) FROM credit_sales WHERE customer_id = ?), 0) as total_credit,
        COALESCE((SELECT SUM(payment_amount) FROM payments WHERE customer_id = ?), 0) as total_paid
");
$stmt->execute([$customer_id, $customer_id]);
$balanceData = $stmt->fetch();
$balance = $balanceData['total_credit'] - $balanceData['total_paid'];

// Get credit sales
$stmt = $pdo->prepare("
    SELECT cs.*, 
        (SELECT SUM(payment_amount) FROM payments WHERE credit_sale_id = cs.id) as paid
    FROM credit_sales cs
    WHERE cs.customer_id = ?
    ORDER BY cs.sale_date DESC
");
$stmt->execute([$customer_id]);
$sales = $stmt->fetchAll();

// Get payments
$stmt = $pdo->prepare("
    SELECT p.*,
        (SELECT full_name FROM customers WHERE id = p.customer_id) as customer_name
    FROM payments p
    WHERE p.customer_id = ?
    ORDER BY p.payment_date DESC
");
$stmt->execute([$customer_id]);
$payments = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<a href="customers.php" class="btn btn-outline mb-3">
    <i class="fas fa-arrow-left"></i> Back to Customers
</a>

<div class="features-grid" style="grid-template-columns: 2fr 1fr;">
    <div>
        <div class="card mb-4">
            <div class="card-header">
                <h3>Customer Details</h3>
            </div>
            <div class="card-body">
                <div class="features-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <div>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email'] ?? '-'); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
                    </div>
                    <div>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['address'] ?? '-'); ?></p>
                        <p><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($customer['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="openTab('sales')">Credit Sales</button>
            <button class="tab" onclick="openTab('payments')">Payments</button>
        </div>

        <div id="sales" class="tab-content active">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Paid</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <?php $salePaid = $sale['paid'] ?? 0; ?>
                    <?php $saleBalance = $sale['total_amount'] - $salePaid; ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></td>
                        <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                        <td><?php echo $sale['due_date'] ? date('M d, Y', strtotime($sale['due_date'])) : '-'; ?></td>
                        <td><?php echo formatCurrency($salePaid); ?></td>
                        <td>
                            <span class="badge <?php echo $saleBalance > 0 ? 'badge-warning' : 'badge-success'; ?>">
                                <?php echo formatCurrency($saleBalance); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No credit sales</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="payments" class="tab-content">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo formatCurrency($payment['payment_amount']); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($payment['notes'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No payments</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card mb-4">
            <div class="card-header">
                <h3>Account Summary</h3>
            </div>
            <div class="card-body">
                <div style="text-align: center; padding: 1rem;">
                    <p style="font-size: 0.875rem; color: var(--gray-500);">Total Credit</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?php echo formatCurrency($balanceData['total_credit']); ?></p>
                </div>
                <hr>
                <div style="text-align: center; padding: 1rem;">
                    <p style="font-size: 0.875rem; color: var(--gray-500);">Total Paid</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--success-color);"><?php echo formatCurrency($balanceData['total_paid']); ?></p>
                </div>
                <hr>
                <div style="text-align: center; padding: 1rem; background: <?php echo $balance > 0 ? '#fef3c7' : '#d1fae5'; ?>; border-radius: 8px;">
                    <p style="font-size: 0.875rem; color: var(--gray-500);">Remaining Balance</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: <?php echo $balance > 0 ? '#d97706' : '#059669'; ?>;"><?php echo formatCurrency($balance); ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="credit_sales.php?customer_id=<?php echo $customer_id; ?>" class="btn btn-primary w-full mb-2">
                    <i class="fas fa-plus"></i> New Credit Sale
                </a>
                <a href="payments.php?customer_id=<?php echo $customer_id; ?>" class="btn btn-success w-full">
                    <i class="fas fa-money-bill-wave"></i> Record Payment
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function openTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    event.target.classList.add('active');
}
</script>
