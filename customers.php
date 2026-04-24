<?php
/**
 * Customers Management Page
 * Business Loan Management System
 */
$pageTitle = 'Customers';
$isDashboardPage = true;
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');

        if (empty($full_name) || empty($phone)) {
            $error = 'Name and phone are required';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO customers (full_name, email, phone, address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$full_name, $email, $phone, $address]);
                $message = 'Customer added successfully';
            } catch (PDOException $e) {
                $error = 'Failed to add customer';
            }
        }
    } elseif ($action == 'edit') {
        $id = $_POST['id'] ?? '';
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');

        if (empty($id) || empty($full_name) || empty($phone)) {
            $error = 'Name and phone are required';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE customers SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $phone, $address, $id]);
                $message = 'Customer updated successfully';
            } catch (PDOException $e) {
                $error = 'Failed to update customer';
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'] ?? '';

        if (!empty($id)) {
            try {
                $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Customer deleted successfully';
            } catch (PDOException $e) {
                $error = 'Failed to delete customer';
            }
        }
    }
}

// Get all customers with balance
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("
        SELECT c.*,
            COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = c.id), 0) as total_credit,
            COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = c.id), 0) as total_paid
        FROM customers c
        WHERE c.full_name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT c.*,
            COALESCE((SELECT SUM(cs.total_amount) FROM credit_sales cs WHERE cs.customer_id = c.id), 0) as total_credit,
            COALESCE((SELECT SUM(p.payment_amount) FROM payments p WHERE p.customer_id = c.id), 0) as total_paid
        FROM customers c
        ORDER BY c.created_at DESC
    ");
}
$customers = $stmt->fetchAll();
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

<div class="search-bar">
    <form method="GET" action="" style="display: flex; gap: 1rem; flex: 1;">
        <input type="text" class="search-input" name="search" placeholder="Search customers..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Search
        </button>
    </form>
    <button class="btn btn-success" onclick="openModal('addCustomerModal')">
        <i class="fas fa-plus"></i> Add Customer
    </button>
</div>

<div class="table-container">
    <table class="table" id="customersTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Total Credit</th>
                <th>Total Paid</th>
                <th>Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
            <?php $balance = $customer['total_credit'] - $customer['total_paid']; ?>
            <tr>
                <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                <td><?php echo htmlspecialchars($customer['email'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                <td><?php echo formatCurrency($customer['total_credit']); ?></td>
                <td><?php echo formatCurrency($customer['total_paid']); ?></td>
                <td>
                    <span class="badge <?php echo $balance > 0 ? 'badge-warning' : 'badge-success'; ?>">
                        <?php echo formatCurrency($balance); ?>
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="edit-btn" onclick="editCustomer(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars($customer['full_name']); ?>', '<?php echo htmlspecialchars($customer['email'] ?? ''); ?>', '<?php echo htmlspecialchars($customer['phone']); ?>', '<?php echo htmlspecialchars($customer['address'] ?? ''); ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-btn" onclick="deleteCustomer(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars($customer['full_name']); ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                        <a href="customer_profile.php?id=<?php echo $customer['id']; ?>" class="edit-btn" title="View Profile">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($customers)): ?>
            <tr>
                <td colspan="7" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>No customers found</h3>
                        <p>Add your first customer to get started</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Customer Modal -->
<div class="modal-overlay" id="addCustomerModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add New Customer</h3>
            <button class="modal-close" onclick="closeModal('addCustomerModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-control" name="full_name" placeholder="Enter customer name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" class="form-control" name="phone" placeholder="Enter phone number" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" placeholder="Enter address" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addCustomerModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal-overlay" id="editCustomerModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Customer</h3>
            <button class="modal-close" onclick="closeModal('editCustomerModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editCustomerId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-control" name="full_name" id="editCustomerName" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="editCustomerEmail">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" class="form-control" name="phone" id="editCustomerPhone" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" id="editCustomerAddress" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editCustomerModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteCustomerModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Delete Customer</h3>
            <button class="modal-close" onclick="closeModal('deleteCustomerModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteCustomerId">
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteCustomerName"></strong>?</p>
                <p class="text-danger">This action cannot be undone. All credit sales and payments for this customer will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('deleteCustomerModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function editCustomer(id, name, email, phone, address) {
    document.getElementById('editCustomerId').value = id;
    document.getElementById('editCustomerName').value = name;
    document.getElementById('editCustomerEmail').value = email;
    document.getElementById('editCustomerPhone').value = phone;
    document.getElementById('editCustomerAddress').value = address;
    openModal('editCustomerModal');
}

function deleteCustomer(id, name) {
    document.getElementById('deleteCustomerId').value = id;
    document.getElementById('deleteCustomerName').textContent = name;
    openModal('deleteCustomerModal');
}
</script>