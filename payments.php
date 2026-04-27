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

// Get customers with balance
$stmt = $pdo->query("
    SELECT c.id, c.full_name, c.phone,
        COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = c.id), 0) as total_credit,
        COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = c.id), 0) as total_paid
    FROM customers c
    HAVING total_credit > total_paid
    ORDER BY c.full_name
");
$customers = $stmt->fetchAll();
$selected_customer_id = filter_var($_GET['customer_id'] ?? null, FILTER_VALIDATE_INT);
$selected_sale_id = filter_var($_GET['sale_id'] ?? null, FILTER_VALIDATE_INT);

if ($selected_sale_id) {
    $stmt = $pdo->prepare("SELECT customer_id FROM credit_sales WHERE id = ?");
    $stmt->execute([$selected_sale_id]);
    $selectedSale = $stmt->fetch();

    if ($selectedSale) {
        $selected_customer_id = (int) $selectedSale['customer_id'];
    } else {
        $selected_sale_id = null;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $customer_id = filter_var($_POST['customer_id'] ?? null, FILTER_VALIDATE_INT);
        $credit_sale_id = filter_var($_POST['credit_sale_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $payment_amount = filter_var($_POST['payment_amount'] ?? null, FILTER_VALIDATE_FLOAT);
        $payment_date = $_POST['payment_date'] ?? date('Y-m-d');
        $payment_method = sanitize($_POST['payment_method'] ?? 'Cash');
        $notes = sanitize($_POST['notes'] ?? '');

        if (empty($customer_id)) {
            $error = 'Please select a customer';
        } elseif ($payment_amount === false || $payment_amount === null) {
            $error = 'Payment amount is required';
        } elseif ($payment_amount <= 0) {
            $error = 'Payment amount must be greater than zero';
        } else {
            if ($credit_sale_id) {
                $stmt = $pdo->prepare("SELECT id FROM credit_sales WHERE id = ? AND customer_id = ?");
                $stmt->execute([$credit_sale_id, $customer_id]);
                if (!$stmt->fetch()) {
                    $error = 'Selected sale does not belong to this customer';
                }
            }

            if (!$error) {
                // Get customer current balance
                $stmt = $pdo->prepare("
                    SELECT COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = ?), 0) as total_credit,
                           COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = ?), 0) as total_paid
                ");
                $stmt->execute([$customer_id, $customer_id]);
                $customer = $stmt->fetch();
                $currentBalance = $customer['total_credit'] - $customer['total_paid'];

                // Check if payment exceeds balance
                if ($payment_amount > $currentBalance) {
                    $error = 'Payment amount cannot exceed the outstanding balance of ' . formatCurrency($currentBalance);
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO payments (customer_id, credit_sale_id, payment_amount, payment_date, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$customer_id, $credit_sale_id, $payment_amount, $payment_date, $payment_method, $notes]);
                        $message = 'Payment of ' . formatCurrency($payment_amount) . ' recorded successfully';
                    } catch (PDOException $e) {
                        $error = 'Failed to record payment';
                    }
                }
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
    SELECT p.*, c.id as customer_id, c.full_name as customer_name, c.phone as customer_phone
    FROM payments p
    JOIN customers c ON p.customer_id = c.id
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<style>
.quick-amounts {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.quick-amount-btn {
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.quick-amount-btn:hover {
    border-color: #1e3a5f;
    background: #f0f4f8;
}

.quick-amount-btn.selected {
    background: #1e3a5f;
    color: white;
    border-color: #1e3a5f;
}

.balance-display {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 16px;
}

.balance-amount {
    font-size: 2rem;
    font-weight: 700;
    color: #92400e;
}

.balance-label {
    font-size: 0.85rem;
    color: #a16207;
}

.partial-info {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 8px;
    font-size: 0.85rem;
    color: #64748b;
}

.partial-info i {
    color: #10b981;
}
</style>

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
        <h3><i class="fas fa-money-bill-wave"></i> Record New Payment</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="" id="paymentForm">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="credit_sale_id" value="<?php echo htmlspecialchars($selected_sale_id ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <div class="features-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="form-group">
                    <label class="form-label">Customer *</label>
                    <select class="form-control form-select" name="customer_id" id="customerSelect" required onchange="loadCustomerData(this.value)">
                        <option value="">Select Customer</option>
                        <?php foreach ($customers as $customer): ?>
                        <?php $balance = $customer['total_credit'] - $customer['total_paid']; ?>
                        <option value="<?php echo (int) $customer['id']; ?>" data-balance="<?php echo htmlspecialchars($balance, ENT_QUOTES, 'UTF-8'); ?>" data-name="<?php echo htmlspecialchars($customer['full_name'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected_customer_id == $customer['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($customer['full_name']); ?> - Balance: <?php echo formatCurrency($balance); ?>
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
                    <input type="number" class="form-control" name="payment_amount" id="paymentAmount" placeholder="0.00" step="0.01" min="0.01" required>
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

            <div class="form-group">
                <label class="form-label">Quick Amount</label>
                <div class="quick-amounts">
                    <button type="button" class="quick-amount-btn" onclick="setAmount(50)">$50</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(100)">$100</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(200)">$200</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(300)">$300</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount('full')">Full Balance</button>
                </div>
            </div>

            <div class="features-grid" style="grid-template-columns: repeat(2, 1fr);">
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" placeholder="Optional notes about this payment" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <div class="balance-display">
                        <div class="balance-label">Outstanding Balance</div>
                        <div class="balance-amount" id="customerBalance">$0.00</div>
                    </div>
                    <button type="submit" class="btn btn-success">
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
let currentBalance = 0;

function loadCustomerData(customerId) {
    const select = document.getElementById('customerSelect');
    const option = select.options[select.selectedIndex];
    currentBalance = parseFloat(option?.dataset?.balance) || 0;
    
    const balanceEl = document.getElementById('customerBalance');
    balanceEl.textContent = '$' + currentBalance.toFixed(2);
    
    // Clear payment amount
    document.getElementById('paymentAmount').value = '';
    
    // Remove selected class from quick buttons
    document.querySelectorAll('.quick-amount-btn').forEach(btn => btn.classList.remove('selected'));
}

function setAmount(amount) {
    const input = document.getElementById('paymentAmount');
    
    if (amount === 'full') {
        input.value = currentBalance.toFixed(2);
    } else {
        input.value = amount;
    }
    
    // Update selected button
    document.querySelectorAll('.quick-amount-btn').forEach(btn => {
        btn.classList.remove('selected');
        if (btn.textContent === '$' + amount || (amount === 'full' && btn.textContent === 'Full Balance')) {
            btn.classList.add('selected');
        }
    });
}

function deletePayment(id) {
    document.getElementById('deletePaymentId').value = id;
    openModal('deletePaymentModal');
}
</script>
<?php if ($selected_customer_id): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCustomerData('<?php echo (int) $selected_customer_id; ?>');
});
</script>
<?php endif; ?>
