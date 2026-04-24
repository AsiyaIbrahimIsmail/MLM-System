<?php
/**
 * Services Page
 * Business Loan Management System
 */
$pageTitle = 'Services';
?>
<?php include 'includes/header.php'; ?>

<section class="section" style="padding-top: 100px;">
    <div class="container">
        <div class="section-header">
            <h2>Our Services</h2>
            <p>Comprehensive credit management solutions for your business</p>
        </div>

        <div class="features-grid">
            <div class="card">
                <div class="card-body">
                    <div class="feature-icon" style="margin: 0 auto 1rem; width: 70px; height: 70px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="text-center">Customer Registration</h3>
                    <p>Register and manage customer accounts with their personal details, contact information, and account history. Keep all your customer information organized and easily accessible.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="feature-icon" style="margin: 0 auto 1rem; width: 70px; height: 70px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="text-center">Credit Sales Recording</h3>
                    <p>Record every credit sale with products, quantities, unit prices, and automatic total calculation. Track each transaction with date and optional notes.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="feature-icon" style="margin: 0 auto 1rem; width: 70px; height: 70px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3 class="text-center">Payment Tracking</h3>
                    <p>Record customer payments and automatically update their remaining balance. Track payment history with date, amount, and payment method.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="feature-icon" style="margin: 0 auto 1rem; width: 70px; height: 70px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="text-center">Balance Calculation</h3>
                    <p>Automatic calculation of total debt, total paid, and remaining balance for each customer. Always know exactly where your accounts stand.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="feature-icon" style="margin: 0 auto 1rem; width: 70px; height: 70px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white;">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="text-center">Financial Reporting</h3>
                    <p>Generate comprehensive reports including outstanding balances, fully paid accounts, daily and monthly summaries, and export capabilities.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="feature-icon" style="margin: 0 auto 1rem; width: 70px; height: 70px; background: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white;">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="text-center">History Management</h3>
                    <p>View complete credit history and payment history for each customer. Track every transaction and payment made over time.</p>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h3>Additional Features</h3>
                <ul style="list-style: disc; padding-left: 20px;">
                    <li>Product inventory management</li>
                    <li>Credit due date tracking</li>
                    <li>Search and filter capabilities</li>
                    <li>Responsive design for all devices</li>
                    <li>Secure authentication system</li>
                    <li>Role-based access (Admin/Staff)</li>
                    <li>Toast notifications</li>
                    <li>Data export to CSV</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>