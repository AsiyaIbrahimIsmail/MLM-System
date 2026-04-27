<?php
/**
 * Get Customer Balance API
 * Returns JSON balance for a customer
 */
require_once 'includes/db.php';

header('Content-Type: application/json');

$customer_id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$customer_id) {
    echo json_encode(['balance' => 0]);
    exit();
}

// Get customer balance
$stmt = $pdo->prepare("
    SELECT 
        COALESCE((SELECT SUM(total_amount) FROM credit_sales WHERE customer_id = ?), 0) as total_credit,
        COALESCE((SELECT SUM(payment_amount) FROM payments WHERE customer_id = ?), 0) as total_paid
");
$stmt->execute([$customer_id, $customer_id]);
$data = $stmt->fetch();

$balance = $data['total_credit'] - $data['total_paid'];

echo json_encode([
    'balance' => $balance,
    'total_credit' => $data['total_credit'],
    'total_paid' => $data['total_paid']
]);
