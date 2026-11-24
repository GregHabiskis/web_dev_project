<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../config/db.php';

if (!isset($adminTitle)) {
    $adminTitle = 'SalesPro Admin';
}
if (!isset($activeAdminNav)) {
    $activeAdminNav = 'dashboard';
}

$pdo = getDbConnection();

// Get unread messages count
$msgCountStmt = $pdo->query("SELECT count(*) FROM contact_messages WHERE is_read = 0");
$unreadMessages = (int)$msgCountStmt->fetchColumn();

// Get pending orders count
$pendingOrdersStmt = $pdo->query("SELECT count(*) FROM orders WHERE status = 'Pending'");
$pendingOrders = (int)$pendingOrdersStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($adminTitle) ?> | SalesPro Admin</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .admin-nav {
      background: #111820;
      color: #fff;
      padding: 0 5%;
      border-bottom: 2px solid #0096d6;
    }
    .admin-nav .wrap {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 64px;
      padding: 0;
    }
    .admin-menu {
      display: flex;
      gap: 20px;
      align-items: center;
    }
    .admin-menu a {
      color: #b9c2c9;
      font-size: 14px;
      font-weight: 600;
      padding: 8px 12px;
      border-radius: 4px;
      transition: all .2s;
      position: relative;
    }
    .admin-menu a:hover, .admin-menu a.active {
      color: #fff;
      background: rgba(255,255,255,0.1);
    }
    .admin-badge {
      display: inline-block;
      padding: 2px 6px;
      font-size: 11px;
      background: #e53e3e;
      color: #fff;
      border-radius: 10px;
      margin-left: 4px;
      font-weight: 700;
    }
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
      font-size: 14px;
    }
    .admin-table th, .admin-table td {
      padding: 12px 14px;
      border-bottom: 1px solid var(--line);
      text-align: left;
    }
    .admin-table th {
      background: var(--soft);
      font-weight: 700;
      color: var(--ink);
    }
    .admin-table tr:hover {
      background: #fafbfc;
    }
    .status-pill {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 700;
    }
    .status-Pending { background: #fef3c7; color: #92400e; }
    .status-Processing { background: #e0f2fe; color: #075985; }
    .status-Shipped { background: #f3e8ff; color: #6b21a8; }
    .status-Delivered { background: #dcfce7; color: #166534; }
    .status-Cancelled { background: #fee2e2; color: #991b1b; }
  </style>
</head>
<body style="background:#f8f9fa;">
  <header class="admin-nav">
    <div class="wrap">
      <div style="display:flex;align-items:center;gap:16px;">
        <a href="index.php" style="display:flex;align-items:center;gap:8px;">
          <strong style="color:#fff;font-size:18px;letter-spacing:1px;">SALESPRO</strong>
          <span style="background:var(--blue);color:#fff;font-size:11px;padding:2px 6px;border-radius:4px;font-weight:700;">ADMIN</span>
        </a>
      </div>
      <nav class="admin-menu">
        <a href="index.php" class="<?= $activeAdminNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="products.php" class="<?= $activeAdminNav === 'products' ? 'active' : '' ?>">Products</a>
        <a href="orders.php" class="<?= $activeAdminNav === 'orders' ? 'active' : '' ?>">
          Orders <?php if ($pendingOrders > 0): ?><span class="admin-badge"><?= $pendingOrders ?></span><?php endif; ?>
        </a>
        <a href="messages.php" class="<?= $activeAdminNav === 'messages' ? 'active' : '' ?>">
          Messages <?php if ($unreadMessages > 0): ?><span class="admin-badge"><?= $unreadMessages ?></span><?php endif; ?>
        </a>
        <a href="subscribers.php" class="<?= $activeAdminNav === 'subscribers' ? 'active' : '' ?>">Subscribers</a>
        <a href="../index.php" target="_blank" style="color:#0096d6;">View Store ↗</a>
        <a href="logout.php" style="color:#e53e3e;">Logout</a>
      </nav>
    </div>
  </header>

