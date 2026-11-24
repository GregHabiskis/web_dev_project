<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$pdo = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_REQUEST['action'] ?? '';

if ($method === 'GET' && ($action === 'get' || $action === '')) {
    $cartDetails = getCartDetails($pdo);
    echo json_encode([
        'success' => true,
        'cart' => $cartDetails
    ]);
    exit;
}

if ($method === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    switch ($action) {
        case 'add':
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                exit;
            }

            // Verify product exists
            $stmt = $pdo->prepare("SELECT id, name FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }

            $qtyToAdd = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qtyToAdd;

            $cartDetails = getCartDetails($pdo);
            echo json_encode([
                'success' => true,
                'message' => $product['name'] . ' added to cart',
                'count' => $cartDetails['count'],
                'cart' => $cartDetails
            ]);
            exit;

        case 'update':
            $change = isset($_POST['change']) ? (int)$_POST['change'] : 0;
            if ($id > 0 && isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] += $change;
                if ($_SESSION['cart'][$id] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
            $cartDetails = getCartDetails($pdo);
            echo json_encode([
                'success' => true,
                'count' => $cartDetails['count'],
                'cart' => $cartDetails
            ]);
            exit;

        case 'remove':
            if ($id > 0 && isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
            }
            $cartDetails = getCartDetails($pdo);
            echo json_encode([
                'success' => true,
                'message' => 'Item removed',
                'count' => $cartDetails['count'],
                'cart' => $cartDetails
            ]);
            exit;

        case 'clear':
            $_SESSION['cart'] = [];
            $cartDetails = getCartDetails($pdo);
            echo json_encode([
                'success' => true,
                'message' => 'Cart cleared',
                'count' => 0,
                'cart' => $cartDetails
            ]);
            exit;

        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
            exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Method not allowed']);
