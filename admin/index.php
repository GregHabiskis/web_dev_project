<?php
$adminTitle = 'Dashboard';
$activeAdminNav = 'dashboard';

require_once __DIR__ . '/includes/header.php';

// Fetch key metrics
$totalSales = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
$totalOrders = $pdo->query("SELECT count(*) FROM orders")->fetchColumn();
$totalProducts = $pdo->query("SELECT count(*) FROM products")->fetchColumn();
$totalMessages = $pdo->query("SELECT count(*) FROM contact_messages")->fetchColumn();
$totalSubscribers = $pdo->query("SELECT count(*) FROM subscribers")->fetchColumn();

// Fetch recent orders
$recentOrdersStmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
$recentOrders = $recentOrdersStmt->fetchAll();

// Fetch recent messages
$recentMessagesStmt = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
$recentMessages = $recentMessagesStmt->fetchAll();
?>

<div class="wrap" style="padding-top:32px;">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;">
    <div>
      <h1 style="font-size:26px;">Dashboard Overview</h1>
      <p style="color:var(--muted);font-size:14px;">Welcome back, <strong><?= e($_SESSION['admin_name'] ?? 'Admin') ?></strong></p>
    </div>
    <div>
      <a href="products.php?action=add" class="button button-blue button-sm">+ Add New Product</a>
    </div>
  </div>

  <!-- KPI Metrics Grid -->
  <div class="grid g4" style="margin-bottom:32px;">
    <div class="panel" style="padding:20px;">
      <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;">Total Sales</span>
      <h2 style="font-size:26px;color:#10b981;margin-top:6px;"><?= formatPrice($totalSales) ?></h2>
      <span style="font-size:12px;color:var(--muted);"><?= (int)$totalOrders ?> orders completed/pending</span>
    </div>

    <div class="panel" style="padding:20px;">
      <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;">Total Orders</span>
      <h2 style="font-size:26px;color:var(--blue-d);margin-top:6px;"><?= (int)$totalOrders ?></h2>
      <a href="orders.php" style="font-size:12px;color:var(--blue);font-weight:600;">View orders →</a>
    </div>

    <div class="panel" style="padding:20px;">
      <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;">Catalog Products</span>
      <h2 style="font-size:26px;color:var(--ink);margin-top:6px;"><?= (int)$totalProducts ?></h2>
      <a href="products.php" style="font-size:12px;color:var(--blue);font-weight:600;">Manage inventory →</a>
    </div>

    <div class="panel" style="padding:20px;">
      <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;">Customer Inquiries</span>
      <h2 style="font-size:26px;color:#f59e0b;margin-top:6px;"><?= (int)$totalMessages ?></h2>
      <a href="messages.php" style="font-size:12px;color:var(--blue);font-weight:600;">Read messages →</a>
    </div>
  </div>

  <!-- Recent Orders Section -->
  <div class="panel" style="margin-bottom:32px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
      <h2 style="font-size:18px;">Recent Orders</h2>
      <a href="orders.php" class="text-link" style="font-size:13px;">View all orders →</a>
    </div>

    <?php if (empty($recentOrders)): ?>
      <p style="color:var(--muted);padding:24px 0;text-align:center;">No orders placed yet. Orders will appear here as soon as customers checkout.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $ord): ?>
            <tr>
              <td><strong><?= e($ord['order_number']) ?></strong></td>
              <td><?= e($ord['customer_name']) ?></td>
              <td><?= e($ord['customer_email']) ?></td>
              <td><strong><?= formatPrice($ord['total_amount']) ?></strong></td>
              <td><span class="status-pill status-<?= e($ord['status']) ?>"><?= e($ord['status']) ?></span></td>
              <td><?= date('M j, Y', strtotime($ord['created_at'])) ?></td>
              <td>
                <a href="order-view.php?id=<?= (int)$ord['id'] ?>" class="button button-ghost button-sm" style="padding:4px 10px;min-height:30px;">Details</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- Recent Contact Inquiries -->
  <div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
      <h2 style="font-size:18px;">Recent Contact Messages</h2>
      <a href="messages.php" class="text-link" style="font-size:13px;">View all messages →</a>
    </div>

    <?php if (empty($recentMessages)): ?>
      <p style="color:var(--muted);padding:24px 0;text-align:center;">No messages received yet.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Sender</th>
            <th>Email</th>
            <th>Topic</th>
            <th>Message</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentMessages as $msg): ?>
            <tr>
              <td><strong><?= e($msg['name']) ?></strong></td>
              <td><?= e($msg['email']) ?></td>
              <td><span class="tag" style="margin:0;"><?= e($msg['topic']) ?></span></td>
              <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($msg['message']) ?></td>
              <td><?= date('M j, Y', strtotime($msg['created_at'])) ?></td>
              <td><?= $msg['is_read'] ? '<span style="color:#10b981;">Read</span>' : '<span style="color:#ef4444;font-weight:700;">New</span>' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

