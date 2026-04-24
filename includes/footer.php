<?php
/**
 * Footer Include File
 * Business Loan Management System
 */
?>
        <?php if (isset($isDashboardPage)): ?>
        </main>
    </div>
    <?php endif; ?>

    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer"></div>

    <footer class="<?php echo isset($isDashboardPage) ? 'dashboard-footer' : 'footer'; ?>">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Business Loan Management</h3>
                    <p>Managing shop credits and customer debts efficiently.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="services.php">Customer Registration</a></li>
                        <li><a href="services.php">Credit Tracking</a></li>
                        <li><a href="services.php">Payment Management</a></li>
                        <li><a href="services.php">Financial Reports</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> support@blmsystem.com</li>
                        <li><i class="fas fa-phone"></i> +1 234 567 890</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Business Street</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Business Loan Management System. All rights reserved.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="assets/js/main.js"></script>
    <?php if (isset($isDashboardPage)): ?>
    <script src="assets/js/charts.js"></script>
    <?php endif; ?>
</body>
</html>