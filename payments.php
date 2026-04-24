<?php
/**
 * Payments Management Page
 * Business Loan Management System
 */
$pageTitle = 'Payments';
$isDashboardPage = true;
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$message = '';
$error = '';

// Get customers for dropdown
$stmt = $pdo->query("
    SELECT c.id, c.full_name, c.phone,
        COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = c.id), 0) as total_credit,
        COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = c.id), 0) as total_paid
    FROM customers c
    ORDER BY c.full_name
");
$customers = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $customer_id = $_POST['customer_id'] ?? '';
        $credit_sale_id = $_POST['credit_sale_id'] ?? null;
        $payment_amount = $_POST['payment_amount'] ?? 0;
        $payment_date = $_POST['payment_date'] ?? date('Y-m-d');
        $payment_method = sanitize($_POST['payment_method'] ?? 'Cash');
        $notes = sanitize($_POST['notes'] ?? '');

        if (empty($customer_id) || empty($payment_amount)) {
            $error = 'Customer and payment amount are required';
        } elseif ($payment_amount <= 0) {
            $error = 'Payment amount must be greater than zero';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO payments (customer_id, credit_sale_id, payment_amount, payment_date, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$customer_id, $credit_sale_id, $payment_amount, $payment_date, $payment_method, $notes]);
                $message = 'Payment recorded successfully';
            } catch (PDOException $e) {
                $error = 'Failed to record payment';
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'] ?? '';

        if (!empty($id)) {
            try {
                $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Payment deleted successfully';
            } catch (PDOException $e) {
                $error = 'Failed to delete payment';
            }
        }
    }
}

// Get all payments
$stmt = $pdo->query("
    SELECT p.*, c.full_name as customer_name, c.phone as customer_phone
    FROM payments p
    JOIN customers c ON p.customer_id = c.id
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($message): ?>
<div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <h3>Record New Payment</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">

            <div class="features-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="form-group">
                    <label class="form-label">Customer *</label>
                    <select class="form-control form-select" name="customer_id" id="customerSelect" required onchange="loadCustomerBalance(this.value)">
                        <option value="">Select Customer</option>
                        <?php foreach ($customers as $customer): ?>
                        <?php $balance = $customer['total_credit'] - $customer['total_paid']; ?>
                        <option value="<?php echo $customer['id']; ?>" data-balance="<?php echo $balance; ?>">
                            <?php echo htmlspecialchars($customer['full_name']); ?> (<?php echo formatCurrency($balance); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Date *</label>
                    <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Amount *</label>
                    <input type="number" class="form-control" name="payment_amount" placeholder="0.00" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control form-select" name="payment_method">
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Mobile Money">Mobile Money</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Card">Card</option>
                    </select>
                </div>
            </div>

            <div class="features-grid" style="grid-template-columns: repeat(2, 1fr);">
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" placeholder="Optional notes" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Outstanding Balance</label>
                    <div id="customerBalance" style="font-size: 1.5rem; font-weight: 700; color: var(--danger-color);">$0.00</div>
                    <button type="submit" class="btn btn-success mt-2">
                        <i class="fas fa-check"></i> Record Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-container">
    <table class="table" id="paymentsTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($payment['customer_phone']); ?></td>
                <td><?php echo formatCurrency($payment['payment_amount']); ?></td>
                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                <td><?php echo htmlspecialchars($payment['notes'] ?? '-'); ?></td>
                <td>
                    <div class="table-actions">
                        <button class="delete-btn" onclick="deletePayment(<?php echo $payment['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($payments)): ?>
            <tr>
                <td colspan="7" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-money-bill-wave"></i>
                        <h3>No payments yet</h3>
                        <p>Record your first payment above</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deletePaymentModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Delete Payment</h3>
            <button class="modal-close" onclick="closeModal('deletePaymentModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deletePaymentId">
            <div class="modal-body">
                <p>Are you sure you want to delete this payment?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('deletePaymentModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function loadCustomerBalance(customerId) {
    const select = document.getElementById('customerSelect');
    const option = select.options[select.selectedIndex];
    const balance = option?.dataset?.balance || 0;
    document.getElementById('customerBalance').textContent = '$' + parseFloat(balance).toFixed(2);
}

function deletePayment(id) {
    document.getElementById('deletePaymentId').value = id;
    openModal('deletePaymentModal');
}
</script>