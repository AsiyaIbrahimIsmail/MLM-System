<?php
/**
 * Products Management Page
 * Business Loan Management System
 */
$pageTitle = 'Products';
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
        $product_name = sanitize($_POST['product_name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $unit_price = $_POST['unit_price'] ?? 0;

        if (empty($product_name) || empty($unit_price)) {
            $error = 'Product name and price are required';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (product_name, description, unit_price) VALUES (?, ?, ?)");
                $stmt->execute([$product_name, $description, $unit_price]);
                $message = 'Product added successfully';
            } catch (PDOException $e) {
                $error = 'Failed to add product';
            }
        }
    } elseif ($action == 'edit') {
        $id = $_POST['id'] ?? '';
        $product_name = sanitize($_POST['product_name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $unit_price = $_POST['unit_price'] ?? 0;

        if (empty($id) || empty($product_name) || empty($unit_price)) {
            $error = 'Product name and price are required';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE products SET product_name = ?, description = ?, unit_price = ? WHERE id = ?");
                $stmt->execute([$product_name, $description, $unit_price, $id]);
                $message = 'Product updated successfully';
            } catch (PDOException $e) {
                $error = 'Failed to update product';
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'] ?? '';

        if (!empty($id)) {
            try {
                $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Product deleted successfully';
            } catch (PDOException $e) {
                $error = 'Failed to delete product';
            }
        }
    }
}

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY product_name");
$products = $stmt->fetchAll();
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
    <button class="btn btn-success" onclick="openModal('addProductModal')">
        <i class="fas fa-plus"></i> Add Product
    </button>
</div>

<div class="table-container">
    <table class="table" id="productsTable">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Unit Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['description'] ?? '-'); ?></td>
                <td><?php echo formatCurrency($product['unit_price']); ?></td>
                <td>
                    <div class="table-actions">
                        <button class="edit-btn" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>', '<?php echo htmlspecialchars($product['description'] ?? ''); ?>', '<?php echo $product['unit_price']; ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-btn" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (empty($products)): ?>
            <tr>
                <td colspan="4" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-box"></i>
                        <h3>No products found</h3>
                        <p>Add your first product to get started</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Product Modal -->
<div class="modal-overlay" id="addProductModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add New Product</h3>
            <button class="modal-close" onclick="closeModal('addProductModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" class="form-control" name="product_name" placeholder="Enter product name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" placeholder="Enter description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" class="form-control" name="unit_price" placeholder="0.00" step="0.01" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addProductModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal-overlay" id="editProductModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <button class="modal-close" onclick="closeModal('editProductModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editProductId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" class="form-control" name="product_name" id="editProductName" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="editProductDescription" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" class="form-control" name="unit_price" id="editProductPrice" step="0.01" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editProductModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteProductModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Delete Product</h3>
            <button class="modal-close" onclick="closeModal('deleteProductModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteProductId">
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('deleteProductModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function editProduct(id, name, description, price) {
    document.getElementById('editProductId').value = id;
    document.getElementById('editProductName').value = name;
    document.getElementById('editProductDescription').value = description;
    document.getElementById('editProductPrice').value = price;
    openModal('editProductModal');
}

function deleteProduct(id, name) {
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteProductName').textContent = name;
    openModal('deleteProductModal');
}
</script>