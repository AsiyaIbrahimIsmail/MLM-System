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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Business Loan Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2d5a8a;
            --primary-dark: #0f1f33;
            --accent: #10b981;
            --accent-light: #34d399;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        
        /* Hero Section */
        .hero {
            padding: 0;
            background:
                linear-gradient(135deg, rgba(15, 31, 51, 0.96) 0%, rgba(30, 58, 95, 0.94) 58%, rgba(16, 185, 129, 0.9) 100%),
                url('https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1800&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
            min-height: 92vh;
            display: flex;
            align-items: center;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(15, 31, 51, 0.5) 0%, rgba(15, 31, 51, 0.12) 55%, rgba(15, 31, 51, 0.32) 100%);
            animation: heroFloat 15s ease-in-out infinite;
        }
        
        @keyframes heroFloat {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, -20px); }
        }
        
        .hero-bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .hero-shape {
            position: absolute;
            border-radius: 24px;
            opacity: 0.08;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .hero-shape-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
            animation: shapeFloat 20s ease-in-out infinite;
        }
        
        .hero-shape-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            left: -50px;
            animation: shapeFloat 25s ease-in-out infinite reverse;
        }
        
        @keyframes shapeFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 30px) scale(1.1); }
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 1180px;
            margin: 0 auto;
            padding: 96px 32px 72px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr);
            gap: 56px;
            align-items: center;
        }
        
        .hero-text {
            color: white;
            animation: fadeInUp 0.8s ease;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .hero-badge i {
            color: var(--accent-light);
        }
        
        .hero h1 {
            font-size: clamp(2.4rem, 5vw, 4.35rem);
            font-weight: 800;
            line-height: 1.04;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #fff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 32px;
            line-height: 1.7;
            max-width: 620px;
        }
        
        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-hero-primary {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
        }
        
        .btn-hero-primary:hover {
            background: var(--accent-light);
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.5);
        }
        
        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-hero-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: white;
            transform: translateY(-4px) scale(1.05);
        }
        
        /* Hero Stats */
.hero-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            animation: fadeInUp 0.8s ease 0.3s both;
        }
        
        .hero-stat-card {
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(20px);
            border-radius: 8px;
            padding: 22px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease both;
        }
        
        .hero-stat-card:nth-child(1) { animation-delay: 0.2s; }
        .hero-stat-card:nth-child(2) { animation-delay: 0.3s; }
        .hero-stat-card:nth-child(3) { animation-delay: 0.4s; }
        .hero-stat-card:nth-child(4) { animation-delay: 0.5s; }
        
        .hero-stat-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .hero-stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        
        .hero-stat-icon.blue {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }
        
        .hero-stat-icon.green {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }
        
        .hero-stat-icon.yellow {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }
        
        .hero-stat-icon.pink {
            background: rgba(236, 72, 153, 0.2);
            color: #f472b6;
        }
        
        .hero-stat-number {
            font-size: 1.55rem;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
            overflow-wrap: anywhere;
        }
        
        .hero-stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Features Section */
        .features {
            padding: 100px 0;
            background: white;
            position: relative;
        }
        
        .features::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(to bottom, #f8fafc, transparent);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .section-tag {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(30, 58, 95, 0.1);
            color: var(--primary);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 16px;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 16px;
        }
        
        .section-header p {
            color: #64748b;
            font-size: 1.1rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }
        
        .feature-card {
            background: white;
            border-radius: 24px;
            padding: 40px 32px;
            transition: all 0.4s ease;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(30, 58, 95, 0.15);
            border-color: transparent;
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 24px;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(10deg);
        }
        
        .feature-icon.blue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }
        
        .feature-icon.green {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .feature-icon.yellow {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .feature-icon.purple {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            color: white;
        }
        
        .feature-icon.pink {
            background: linear-gradient(135deg, #ec4899, #db2777);
            color: white;
        }
        
        .feature-icon.red {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .feature-card h3 {
            font-size: 1.35rem;
            color: var(--primary);
            margin-bottom: 12px;
        }
        
        .feature-card p {
            color: #64748b;
            line-height: 1.7;
        }
        
        /* Calculator Section */
        .calculator-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            position: relative;
            overflow: hidden;
        }
        
        .calculator-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(16, 185, 129, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(245, 158, 11, 0.1) 0%, transparent 50%);
        }
        
        .calculator-section .section-tag {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }
        
        .calculator-section h2 {
            color: white;
        }
        
        .calculator-section p {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .calculator-card {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 1;
        }
        
        .calc-input-group {
            margin-bottom: 20px;
        }
        
        .calc-input-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .calc-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .calc-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
        }
        
        .calc-results {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 32px;
        }
        
        .calc-result {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
        }
        
        .calc-result-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 8px;
        }
        
        .calc-result-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .calc-result-value.danger {
            color: #ef4444;
        }
        
        .calc-result-value.success {
            color: #10b981;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: #f8fafc;
            text-align: center;
        }
        
        .cta-card {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 32px;
            padding: 60px 48px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .cta-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }
        
        .cta-card h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 16px;
        }
        
        .cta-card p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 32px;
        }
        
        .btn-cta {
            padding: 18px 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(30, 58, 95, 0.3);
        }
        
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(30, 58, 95, 0.4);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 96px 24px 56px;
                gap: 40px;
            }
            
            .hero-text {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .hero-subtitle {
                max-width: 100%;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .hero-stats {
                width: min(100%, 680px);
                margin: 0 auto;
            }
        }
        
        @media (max-width: 768px) {
            .hero {
                min-height: auto;
                padding: 28px 0 40px;
            }
            
            .hero h1 {
                font-size: 2.05rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-badge {
                font-size: 0.75rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-hero {
                width: 100%;
                justify-content: center;
            }
            
            .hero-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            
            .hero-stat-card {
                padding: 20px;
            }
            
            .hero-stat-number {
                font-size: 1.5rem;
            }
            
            .hero-shape {
                display: none;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .calculator-card {
                padding: 24px 20px;
                margin: 0 16px;
            }
            
            .calc-results {
                grid-template-columns: 1fr;
            }
            
            .cta-card {
                padding: 40px 24px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                padding: 0 20px;
            }
        }
        
        @media (max-width: 576px) {
            .hero h1 {
                font-size: 1.75rem;
            }
            
            .hero-stats {
                gap: 16px;
                grid-template-columns: 1fr;
            }
            
            .hero-stat-card {
                padding: 16px;
            }
            
            .hero-stat-number {
                font-size: 1.1rem;
            }
            
            .calculator-card {
                padding: 20px 16px;
            }
            
            .calc-input {
                font-size: 0.9rem;
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg-shapes">
            <div class="hero-shape hero-shape-1"></div>
            <div class="hero-shape hero-shape-2"></div>
        </div>
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-badge">
                    <i class="fas fa-star"></i>
                    Trusted by 500+ Businesses
                </div>
                <h1>Smart Credit Management for Your Business</h1>
                <p class="hero-subtitle">Track customer debts, manage credit sales, and collect payments - all in one powerful platform. Streamline your shop's financial operations with ease.</p>
                <div class="hero-buttons">
                    <a href="login.php" class="btn-hero btn-hero-primary">
                        <i class="fas fa-sign-in-alt"></i> Get Started
                    </a>
                    <a href="#features" class="btn-hero btn-hero-outline">
                        <i class="fas fa-info-circle"></i> Learn More
                    </a>
                </div>
            </div>
            <div class="hero-stats">
                <div class="hero-stat-card">
                    <div class="hero-stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="hero-stat-number"><?php echo $stats['total_customers']; ?></div>
                    <div class="hero-stat-label">Active Customers</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-icon green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="hero-stat-number"><?php echo formatCurrency($stats['total_credit']); ?></div>
                    <div class="hero-stat-label">Total Credit Given</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-icon yellow">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="hero-stat-number"><?php echo formatCurrency($stats['total_paid']); ?></div>
                    <div class="hero-stat-label">Total Collected</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-icon pink">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="hero-stat-number"><?php echo formatCurrency($stats['remaining_balance']); ?></div>
                    <div class="hero-stat-label">Outstanding Balance</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Features</span>
                <h2>Everything You Need to Manage Your Business</h2>
                <p>Powerful tools to streamline your credit management, track payments, and grow your business</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Easily register and manage customer profiles with contact details and payment history.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>Credit Sales</h3>
                    <p>Record credit sales with products, quantities, and automatic total calculation.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon yellow">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Payment Tracking</h3>
                    <p>Record payments and automatically update customer balances in real-time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon purple">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3>Financial Reports</h3>
                    <p>Generate detailed reports on credit, payments, and outstanding balances.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon pink">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Quick Search</h3>
                    <p>Find any customer instantly with powerful search and filter options.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon red">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Password-protected access with session management and data encryption.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Calculator Section -->
    <section class="calculator-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Tools</span>
                <h2>Credit Calculator</h2>
                <p>Calculate totals and remaining balances instantly</p>
            </div>
            <div class="calculator-card">
                <div class="calc-input-group">
                    <label>Product Price ($)</label>
                    <input type="number" class="calc-input" id="calcPrice" placeholder="Enter unit price" step="0.01" min="0">
                </div>
                <div class="calc-input-group">
                    <label>Quantity</label>
                    <input type="number" class="calc-input" id="calcQty" placeholder="Enter quantity" min="1" value="1">
                </div>
                <div class="calc-input-group">
                    <label>Amount Paid ($)</label>
                    <input type="number" class="calc-input" id="calcPaid" placeholder="Enter amount paid" step="0.01" min="0" value="0">
                </div>
                <div class="calc-results">
                    <div class="calc-result">
                        <div class="calc-result-label">Total Cost</div>
                        <div class="calc-result-value" id="calcTotal">$0.00</div>
                    </div>
                    <div class="calc-result">
                        <div class="calc-result-label">Remaining</div>
                        <div class="calc-result-value" id="calcBalance">$0.00</div>
                    </div>
                </div>
                <div class="calc-help" style="margin-top: 20px; padding: 16px; background: rgba(255,255,255,0.1); border-radius: 12px; color: white; font-size: 0.9rem;">
                    <p style="font-weight: 600; margin-bottom: 8px;"><i class="fas fa-info-circle"></i> How to use:</p>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li><strong>Total Cost</strong> = Price × Quantity (automatically calculated)</li>
                        <li><strong>Remaining</strong> = Total Cost - Amount Paid</li>
                        <li>If Remaining = $0.00, the customer has paid in full</li>
                        <li>If Remaining > $0.00, there is still a balance owed</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2>Ready to Get Started?</h2>
                <p>Join hundreds of businesses already using our platform to manage their credit operations efficiently.</p>
                <a href="login.php" class="btn-cta">
                    <i class="fas fa-rocket"></i> Login to Dashboard
                </a>
            </div>
        </div>
    </section>

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
        
        const balanceEl = document.getElementById('calcBalance');
        if (balance > 0) {
            balanceEl.className = 'calc-result-value danger';
        } else {
            balanceEl.className = 'calc-result-value success';
        }
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    </script>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
