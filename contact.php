<?php
/**
 * Contact Page
 * Business Loan Management System
 */
$pageTitle = 'Contact Us';
?>
<?php include 'includes/header.php'; ?>

<section class="section" style="padding-top: 100px;">
    <div class="container">
        <div class="section-header">
            <h2>Contact Us</h2>
            <p>Get in touch with us for any questions or support</p>
        </div>

        <div class="features-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div class="card">
                <div class="card-body">
                    <h3>Send us a Message</h3>
                    <form id="contactForm" method="POST" action="process_contact.php">
                        <div class="form-group">
                            <label class="form-label">Your Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" placeholder="Enter subject" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="5" placeholder="Enter your message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Contact Information</h3>
                    <div style="margin-bottom: 1.5rem;">
                        <h4><i class="fas fa-envelope text-primary"></i> Email</h4>
                        <p>support@blmsystem.com</p>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <h4><i class="fas fa-phone text-primary"></i> Phone</h4>
                        <p>+1 234 567 890</p>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <h4><i class="fas fa-map-marker-alt text-primary"></i> Address</h4>
                        <p>123 Business Street<br>City, State 12345</p>
                    </div>
                    <div>
                        <h4>Business Hours</h4>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 2:00 PM<br>Sunday: Closed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h3>Find Us</h3>
                <div style="background: var(--gray-100); height: 300px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <div style="text-align: center;">
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                        <p class="text-muted">Map placeholder - integrate with Google Maps API for live map</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>