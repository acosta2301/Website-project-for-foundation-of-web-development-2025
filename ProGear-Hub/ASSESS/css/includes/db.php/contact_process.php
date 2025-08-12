<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

require __DIR__ . '/includes/db.php';

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    exit('All fields are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email format.');
}

$stmt = $conn->prepare('INSERT INTO contact_us (name, email, subject, message) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $name, $email, $subject, $message);

if ($stmt->execute()) {
    header('Location: contact.php?sent=1');
    exit;
} else {
    exit('Error: ' . $stmt->error);
}
