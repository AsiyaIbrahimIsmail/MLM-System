# Business Loan Management System

A comprehensive credit and debt management system for shops and supermarkets.

## Introduction

Business Loan Management System is a simple web-based application built with PHP and MySQL to help businesses manage customer credit, payments, and financial records. It allows users to register customers, record credit sales, track payments, and view reports in one place.

The system is designed for small shops and supermarkets that want an easy way to monitor debts, balances, and daily business transactions.

## Features

- Customer registration and management
- Credit sales tracking with products and quantities
- Payment recording and balance calculation
- Financial reports and analytics
- Secure authentication system
- Responsive design for all devices

## Setup Instructions

### Prerequisites

- XAMPP (or any PHP/MySQL environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Edge, etc.)

### Installation Steps

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Import Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Click "Import" tab
   - Select the SQL file: `database/business_loan_management.sql`
   - Click "Go" to import

3. **Configure Database Connection**
   - Open `includes/db.php`
   - If needed, modify these constants:
     ```php
     define('DB_USER', 'root');    // Your MySQL username
     define('DB_PASS', '');        // Your MySQL password
     ```

4. **Access the System**
   - Open your browser and go to: http://localhost/BussinesLoan/

### Demo Credentials

After importing the database, you can login with:

- **Admin Account**
  - Email: admin@businessloan.com
  - Password: password

- **Staff Account**
  - Email: staff@businessloan.com
  - Password: password

## Project Structure

```
BussinesLoan/
├── index.php              # Home page
├── about.php              # About page
├── services.php            # Services page
├── contact.php            # Contact page
├── login.php              # Login page
├── register.php           # Registration page
├── dashboard.php           # Main dashboard
├── customers.php           # Customer management
├── products.php           # Product management
├── credit_sales.php       # Credit sales management
├── payments.php           # Payment management
├── reports.php            # Reports page
├── customer_profile.php   # Customer profile view
├── view_sale.php          # Sale details view
├── get_customer_balance.php # API for customer balance
├── process_contact.php    # Contact form handler
├── includes/
│   ├── db.php            # Database connection
│   ├── auth.php          # Authentication functions
│   ├── header.php        # Header template
│   └── footer.php        # Footer template
├── assets/
│   ├── css/
│   │   └── style.css    # Main stylesheet
│   ├── js/
│   │   ├── main.js      # Main JavaScript
│   │   └── charts.js   # Chart.js configuration
│   └── images/          # Images folder
├── database/
│   └── business_loan_management.sql # Database file
└── README.md           # This file
```

## Usage Guide

### Adding a Customer
1. Login to the dashboard
2. Go to "Customers" menu
3. Click "Add Customer"
4. Fill in the customer details
5. Click "Add Customer"

### Recording a Credit Sale
1. Go to "Credit Sales" menu
2. Select a customer
3. Add products and quantities
4. The total is calculated automatically
5. Click "Record Credit Sale"

### Recording a Payment
1. Go to "Payments" menu
2. Select a customer
3. Enter payment amount and date
4. Click "Record Payment"

### Viewing Reports
1. Go to "Reports" menu
2. Choose report type (Summary, Outstanding, Daily, Monthly)
3. Filter by date range if needed
4. Export to CSV if needed

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Charts**: Chart.js
- **Icons**: Font Awesome

## Support

For issues or questions, contact: support@blmsystem.com
