<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

require __DIR__ . '/includes/db.php';

// Preços oficiais no servidor
$priceMap = [
    'Red Football'   => 49.90,
    'Tennis Racket'  => 79.90,
    'Running Shoes'  => 39.90,
];

$customer_name = trim($_POST['customer_name'] ?? '');
$email         = trim($_POST['email'] ?? '');
$product_name  = trim($_POST['product_name'] ?? '');
$quantity      = (int)($_POST['quantity'] ?? 0);

if ($customer_name === '' || $email === '' || $product_name === '' || $quantity <= 0) {
    exit('Please fill all fields with valid values.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email format.');
}

if (!isset($priceMap[$product_name])) {
    exit('Invalid product selected.');
}

$price = $priceMap[$product_name];

$stmt = $conn->prepare('INSERT INTO orders (customer_name, email, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssii', $customer_name, $email, $product_name, $quantity, $price);

if ($stmt->execute()) {
    header('Location: orders.php?ok=1');
    exit;
} else {
    exit('Error: ' . $stmt->error);
}
