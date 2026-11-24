<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("INSERT IGNORE INTO subscribers (email) VALUES (?)");
    $stmt->execute([$email]);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for subscribing! Stay tuned for news and offers.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Could not save subscription. Please try again later.'
    ]);
}
