<?php
/**
 * Home Page
 * Business Loan Management System
 */
$pageTitle = 'Home';
require_once 'includes/db.php';
require_once 'includes/auth.php';

$stats = getDashboardStats();
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Business Loan Management System</h1>
            <p class="hero-subtitle">Efficiently manage your shop credits and customer debts with our comprehensive platform. Track sales, payments, and balances all in one place.</p>
            <a href="login.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-sign-in-alt"></i> Get Started
            </a>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-number"><?php echo $stats['total_customers']; ?></div>
                    <div class="hero-stat-label">Registered Customers</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number"><?php echo formatCurrency($stats['total_credit']); ?></div>
                    <div class="hero-stat-label">Total Credit Given</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number"><?php echo formatCurrency($stats['total_paid']); ?></div>
                    <div class="hero-stat-label">Total Collected</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number"><?php echo formatCurrency($stats['remaining_balance']); ?></div>
                    <div class="hero-stat-label">Outstanding Balance</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features section">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose Our System?</h2>
            <p>Everything you need to manage your shop credit operations efficiently</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Easy Customer Registration</h3>
                <p>Register new customers quickly with their contact details and account information.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Credit Sales Tracking</h3>
                <p>Record every credit sale with products, quantities, prices, and automatic total calculation.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3>Payment Management</h3>
                <p>Record customer payments and automatically update their remaining balance.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Financial Reports</h3>
                <p>Generate comprehensive reports on credit issued, payments collected, and outstanding balances.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Fast Customer Lookup</h3>
                <p>Quickly search and find any customer with their complete credit and payment history.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Records</h3>
                <p>All your data is stored securely with password protection and session management.</p>
            </div>
        </div>
    </div>
</section>

<!-- Calculator Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <h2>Credit Calculator</h2>
            <p>Calculate total costs and remaining balances instantly</p>
        </div>

        <div class="calculator" style="max-width: 500px; margin: 0 auto;">
            <div class="form-group">
                <label class="form-label">Product Price ($)</label>
                <input type="number" class="form-control" id="calcPrice" placeholder="Enter unit price" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" id="calcQty" placeholder="Enter quantity" min="1" value="1">
            </div>
            <div class="form-group">
                <label class="form-label">Amount Paid (if any)</label>
                <input type="number" class="form-control" id="calcPaid" placeholder="Enter amount paid" step="0.01" min="0" value="0">
            </div>

            <div class="calculator-result">
                <div class="result-label">Total Cost</div>
                <div class="result-value" id="calcTotal">$0.00</div>
            </div>

            <div class="calculator-result">
                <div class="result-label">Remaining Balance</div>
                <div class="result-value" id="calcBalance">$0.00</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Ready to Get Started?</h2>
            <p>Start managing your shop credits efficiently today</p>
        </div>
        <div style="text-align: center;">
            <a href="login.php" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
document.getElementById('calcPrice')?.addEventListener('input', calculateCalc);
document.getElementById('calcQty')?.addEventListener('input', calculateCalc);
document.getElementById('calcPaid')?.addEventListener('input', calculateCalc);

function calculateCalc() {
    const price = parseFloat(document.getElementById('calcPrice')?.value) || 0;
    const qty = parseFloat(document.getElementById('calcQty')?.value) || 0;
    const paid = parseFloat(document.getElementById('calcPaid')?.value) || 0;
    const total = price * qty;
    const balance = total - paid;

    document.getElementById('calcTotal').textContent = '$' + total.toFixed(2);
    document.getElementById('calcBalance').textContent = '$' + balance.toFixed(2);
    document.getElementById('calcBalance').style.color = balance > 0 ? '#ef4444' : '#10b981';
}
</script>