<?php
/**
 * Credit Sales Management Page
 * Business Loan Management System
 */
$pageTitle = 'Credit Sales';
$isDashboardPage = true;
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$message = '';
$error = '';

// Get customers for dropdown
$stmt = $pdo->query("SELECT id, full_name, phone FROM customers ORDER BY full_name");
$customers = $stmt->fetchAll();

// Get products for dropdown
$stmt = $pdo->query("SELECT id, product_name, unit_price FROM products ORDER BY product_name");
$products = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $customer_id = $_POST['customer_id'] ?? '';
        $sale_date = $_POST['sale_date'] ?? date('Y-m-d');
        $due_date = $_POST['due_date'] ?? null;
        $notes = sanitize($_POST['notes'] ?? '');

        // Get product details from form
        $product_ids = $_POST['product_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unit_prices = $_POST['unit_price'] ?? [];

        if (empty($customer_id) || empty($product_ids)) {
            $error = 'Customer and at least one product are required';
        } else {
            try {
                $pdo->beginTransaction();

                // Calculate total amount
                $total_amount = 0;
                foreach ($product_ids as $index => $product_id) {
                    $qty = intval($quantities[$index] ?? 1);
                    $price = floatval($unit_prices[$index] ?? 0);
                    $total_amount += $qty * $price;
                }

                // Insert credit sale
                $stmt = $pdo->prepare("INSERT INTO credit_sales (customer_id, sale_date, total_amount, due_date, notes) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$customer_id, $sale_date, $total_amount, $due_date, $notes]);
                $credit_sale_id = $pdo->lastInsertId();

                // Insert sale items
                foreach ($product_ids as $index => $product_id) {
                    if (empty($product_id)) continue;

                    $qty = intval($quantities[$index] ?? 1);
                    $price = floatval($unit_prices[$index] ?? 0);
                    $subtotal = $qty * $price;

                    $stmt = $pdo->prepare("INSERT INTO credit_sale_items (credit_sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$credit_sale_id, $product_id, $qty, $price, $subtotal]);
                }

                $pdo->commit();
                $message = 'Credit sale recorded successfully';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = 'Failed to add credit sale: ' . $e->getMessage();
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'] ?? '';

        if (!empty($id)) {
            try {
                $stmt = $pdo->prepare("DELETE FROM credit_sales WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Credit sale deleted successfully';
            } catch (PDOException $e) {
                $error = 'Failed to delete credit sale';
            }
        }
    }
}

// Get all credit sales
$stmt = $pdo->query("
    SELECT cs.*, c.full_name as customer_name, c.phone as customer_phone
    FROM credit_sales cs
    JOIN customers c ON cs.customer_id = c.id
    ORDER BY cs.sale_date DESC
");
$sales = $stmt->fetchAll();
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
        <h3>New Credit Sale</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="" id="creditSaleForm">
            <input type="hidden" name="action" value="add">

            <div class="features-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="form-group">
                    <label class="form-label">Customer *</label>
                    <select class="form-control form-select" name="customer_id" id="customerSelect" required onchange="loadCustomerBalance(this.value)">
                        <option value="">Select Customer</option>
                        <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sale Date *</label>
                    <input type="date" class="form-control" name="sale_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" class="form-control" name="due_date">
                </div>
                <div class="form-group">
                    <label class="form-label">Current Balance</label>
                    <div style="padding: 0.5rem; background: var(--gray-100); border-radius: 8px;">
                        <span id="customerBalance" style="font-weight: 700; color: var(--primary-color);">$0.00</span>
                    </div>
                </div>
            </div>

            <h4 style="margin-top: 1rem; margin-bottom: 1rem;">Products</h4>

            <div id="itemsContainer">
                <div class="credit-sale-item-row features-grid" style="grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr; align-items: end;">
                    <div class="form-group">
                        <label class="form-label">Product</label>
                        <select class="form-control form-select product-select" name="product_id[]" onchange="updatePrice(this)">
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['unit_price']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control item-quantity" name="quantity[]" value="1" min="1" oninput="updateSubtotal(this)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit Price</label>
                        <input type="number" class="form-control item-price" name="unit_price[]" value="0" step="0.01" min="0" oninput="updateSubtotal(this)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtotal</label>
                        <div class="item-subtotal" style="padding: 0.5rem; background: var(--gray-100); border-radius: 8px; font-weight: 600;">$0.00</div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)" style="display: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline mt-2" onclick="addItemRow()">
                <i class="fas fa-plus"></i> Add Another Item
            </button>

            <div class="features-grid" style="grid-template-columns: repeat(2, 1fr); margin-top: 1rem;">
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" placeholder="Optional notes" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Amount</label>
                    <div id="grandTotal" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">$0.00</div>
                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-save"></i> Record Credit Sale
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-container">
    <table class="table" id="salesTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Total Amount</th>
                <th>Due Date</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></td>
                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($sale['customer_phone']); ?></td>
                <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                <td><?php echo $sale['due_date'] ? date('M d, Y', strtotime($sale['due_date'])) : '-'; ?></td>
                <td><?php echo htmlspecialchars($sale['notes'] ?? '-'); ?></td>
                <td>
                    <div class="table-actions">
                        <a href="view_sale.php?id=<?php echo $sale['id']; ?>" class="edit-btn" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="delete-btn" onclick="deleteSale(<?php echo $sale['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($sales)): ?>
            <tr>
                <td colspan="7" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>No credit sales yet</h3>
                        <p>Record your first credit sale above</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteSaleModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Delete Credit Sale</h3>
            <button class="modal-close" onclick="closeModal('deleteSaleModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteSaleId">
            <div class="modal-body">
                <p>Are you sure you want to delete this credit sale?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('deleteSaleModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const productsData = <?php echo json_encode($products); ?>;

function loadCustomerBalance(customerId) {
    if (!customerId) {
        document.getElementById('customerBalance').textContent = '$0.00';
        return;
    }

    fetch('get_customer_balance.php?id=' + customerId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('customerBalance').textContent = '$' + parseFloat(data.balance || 0).toFixed(2);
        })
        .catch(error => console.error('Error:', error));
}

function addItemRow() {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('div');
    row.className = 'credit-sale-item-row features-grid';
    row.style.gridTemplateColumns = '2fr 1fr 1fr 1fr 0.5fr';
    row.style.alignItems = 'end';
    row.style.marginTop = '1rem';

    let options = '<option value="">Select Product</option>';
    productsData.forEach(p => {
        options += `<option value="${p.id}" data-price="${p.unit_price}">${p.product_name}</option>`;
    });

    row.innerHTML = `
        <div class="form-group">
            <select class="form-control form-select product-select" name="product_id[]" onchange="updatePrice(this)">
                ${options}
            </select>
        </div>
        <div class="form-group">
            <input type="number" class="form-control item-quantity" name="quantity[]" value="1" min="1" oninput="updateSubtotal(this)">
        </div>
        <div class="form-group">
            <input type="number" class="form-control item-price" name="unit_price[]" value="0" step="0.01" min="0" oninput="updateSubtotal(this)">
        </div>
        <div class="form-group">
            <div class="item-subtotal" style="padding: 0.5rem; background: var(--gray-100); border-radius: 8px; font-weight: 600;">$0.00</div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    container.appendChild(row);
}

function removeItemRow(button) {
    const rows = document.querySelectorAll('.credit-sale-item-row');
    if (rows.length > 1) {
        button.closest('.credit-sale-item-row').remove();
        calculateGrandTotal();
    }
}

function updatePrice(select) {
    const row = select.closest('.credit-sale-item-row');
    const price = select.options[select.selectedIndex]?.dataset?.price || 0;
    row.querySelector('.item-price').value = price;
    updateSubtotal(select);
}

function updateSubtotal(input) {
    const row = input.closest('.credit-sale-item-row');
    const qty = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const subtotal = qty * price;
    row.querySelector('.item-subtotal').textContent = '$' + subtotal.toFixed(2);
    calculateGrandTotal();
}

function calculateGrandTotal() {
    const rows = document.querySelectorAll('.credit-sale-item-row');
    let total = 0;
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
        const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
        total += qty * price;
    });
    document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
}

function deleteSale(id) {
    document.getElementById('deleteSaleId').value = id;
    openModal('deleteSaleModal');
}
</script>