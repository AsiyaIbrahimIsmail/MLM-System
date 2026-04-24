<?php
/**
 * Process Contact Form
 * Business Loan Management System
 */
require_once 'includes/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message_text = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = 'Please fill in all fields';
    } else {
        // In a real system, this would send an email or save to database
        // For demo purposes, just show success
        $message = 'Thank you for your message! We will get back to you soon.';
    }
}

if ($error) {
    header('Location: contact.php?error=' . urlencode($error));
} else {
    header('Location: contact.php?success=' . urlencode($message));
}
exit();