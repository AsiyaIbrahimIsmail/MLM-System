<?php
/**
 * View Credit Sale Details
 * Business Loan Management System
 */
$pageTitle = 'Sale Details';
$isDashboardPage = true;
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$sale_id = $_GET['id'] ?? 0;

if (!$sale_id) {
    header('Location: credit_sales.php');
    exit();
}

// Get sale details
$stmt = $pdo->prepare("
    SELECT cs.*, c.full_name as customer_name, c.phone as customer_phone, c.address as customer_address
    FROM credit_sales cs
    JOIN customers c ON cs.customer_id = c.id
    WHERE cs.id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();

if (!$sale) {
    header('Location: credit_sales.php');
    exit();
}

// Get sale items
$stmt = $pdo->prepare("
    SELECT csi.*, p.product_name
    FROM credit_sale_items csi
    JOIN products p ON csi.product_id = p.id
    WHERE csi.credit_sale_id = ?
");
$stmt->execute([$sale_id]);
$items = $stmt->fetchAll();

// Get payments for this sale
$stmt = $pdo->prepare("
    SELECT * FROM payments WHERE credit_sale_id = ? OR customer_id = ?
    ORDER BY payment_date DESC
");
$stmt->execute([$sale_id, $sale['customer_id']]);
$payments = $stmt->fetchAll();

// Calculate paid amount
$paid = 0;
foreach ($payments as $p) {
    $paid += $p['payment_amount'];
}
?>
<?php include 'includes/header.php'; ?>

<a href="credit_sales.php" class="btn btn-outline mb-3">
    <i class="fas fa-arrow-left"></i> Back to Credit Sales
</a>

<div class="features-grid" style="grid-template-columns: 2fr 1fr;">
    <div>
        <div class="card mb-4">
            <div class="card-header">
                <h3>Sale #<?php echo $sale_id; ?> Details</h3>
            </div>
            <div class="card-body">
                <div class="features-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <div>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($sale['customer_name']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($sale['customer_phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($sale['customer_address'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p><strong>Sale Date:</strong> <?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></p>
                        <p><strong>Due Date:</strong> <?php echo $sale['due_date'] ? date('M d, Y', strtotime($sale['due_date'])) : 'Not set'; ?></p>
                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($sale['notes'] ?? '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mb-3">Items</h3>
        <div class="table-container mb-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo formatCurrency($item['unit_price']); ?></td>
                        <td><?php echo formatCurrency($item['subtotal']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td><strong><?php echo formatCurrency($sale['total_amount']); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div>
        <div class="card mb-4">
            <div class="card-header">
                <h3>Payment Summary</h3>
            </div>
            <div class="card-body">
                <div style="text-align: center; padding: 1rem;">
                    <p style="font-size: 0.875rem; color: var(--gray-500);">Sale Total</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?php echo formatCurrency($sale['total_amount']); ?></p>
                </div>
                <hr>
                <div style="text-align: center; padding: 1rem;">
                    <p style="font-size: 0.875rem; color: var(--gray-500);">Amount Paid</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--success-color);"><?php echo formatCurrency($paid); ?></p>
                </div>
                <hr>
                <div style="text-align: center; padding: 1rem; background: <?php echo ($sale['total_amount'] - $paid) > 0 ? '#fef3c7' : '#d1fae5'; ?>; border-radius: 8px;">
                    <p style="font-size: 0.875rem; color: var(--gray-500);">Remaining</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: <?php echo ($sale['total_amount'] - $paid) > 0 ? '#d97706' : '#059669'; ?>;"><?php echo formatCurrency($sale['total_amount'] - $paid); ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="payments.php?customer_id=<?php echo $sale['customer_id']; ?>" class="btn btn-success w-full mb-2">
                    <i class="fas fa-money-bill-wave"></i> Record Payment
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>