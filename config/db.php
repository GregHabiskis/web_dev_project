<?php
/**
 * SalesPro Database Configuration & Helper Functions
 * Database: salespro
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'salespro');
define('DB_PORT', '3306');

/**
 * Get PDO Database Connection
 * @return PDO
 */
function getDbConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Attempt to connect without dbname and create it if missing
            try {
                $rootDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $subEx) {
                die("Database connection failed: " . htmlspecialchars($e->getMessage()));
            }
        }
    }
    return $pdo;
}

/**
 * Format currency
 */
function formatPrice($number) {
    return '$' . number_format((float)$number, 2);
}

/**
 * Sanitize string for output
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Total item count in session cart
 */
function getCartCount() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}

/**
 * Fetch detailed cart items with database prices & subtotal
 */
function getCartDetails($pdo) {
    $cart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $items = [];
    $subtotal = 0.0;

    if (!empty($cart)) {
        $ids = array_map('intval', array_keys($cart));
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.id IN ($placeholders)");
            $stmt->execute($ids);
            $products = $stmt->fetchAll();

            $productsById = [];
            foreach ($products as $p) {
                $productsById[$p['id']] = $p;
            }

            foreach ($cart as $id => $qty) {
                $id = (int)$id;
                $qty = (int)$qty;
                if ($qty > 0 && isset($productsById[$id])) {
                    $prod = $productsById[$id];
                    $lineTotal = $prod['price'] * $qty;
                    $subtotal += $lineTotal;
                    $items[] = [
                        'id' => $id,
                        'name' => $prod['name'],
                        'slug' => $prod['slug'],
                        'price' => (float)$prod['price'],
                        'quantity' => $qty,
                        'line_total' => $lineTotal,
                        'image_url' => $prod['image_url'],
                        'category' => $prod['category_name'] ?? 'General'
                    ];
                }
            }
        }
    }

    $shipping = ($subtotal > 500 || $subtotal == 0) ? 0.00 : 25.00;
    $total = $subtotal + $shipping;

    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total' => $total,
        'count' => array_sum(array_column($items, 'quantity'))
    ];
}

/**
 * Flash message helper
 */
function setFlash($key, $message, $type = 'success') {
    $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
}

function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    return null;
}

