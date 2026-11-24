<?php
$adminTitle = 'Order Details';
$activeAdminNav = 'orders';

require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Fetch order items
$itemStmt = $pdo->prepare("SELECT oi.*, p.image_url 
                           FROM order_items oi 
                           LEFT JOIN products p ON oi.product_id = p.id 
                           WHERE oi.order_id = ?");
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll();

$message = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_status'])) {
    $newStatus = trim($_POST['status'] ?? '');
    $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $updateStmt->execute([$newStatus, $order['id']]);
    $order['status'] = $newStatus;
    $message = "Status updated to {$newStatus}.";
}
?>

<div class="wrap" style="padding-top:32px;">
  <?php if (!empty($message)): ?>
    <div style="background:#def7ec;color:#03543f;padding:12px 18px;border-radius:var(--r);margin-bottom:20px;font-weight:600;">
      <?= e($message) ?>
    </div>
  <?php endif; ?>

  <div class="panel" style="max-width:860px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;border-bottom:1px solid var(--line);padding-bottom:16px;flex-wrap:wrap;gap:12px;">
      <div>
        <a href="orders.php" style="color:var(--blue-d);font-size:13px;font-weight:600;">← Back to Orders</a>
        <h1 style="font-size:24px;margin-top:6px;">Order <?= e($order['order_number']) ?></h1>
        <span style="font-size:13px;color:var(--muted);">Placed: <?= date('F j, Y, g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div>
        <form method="POST" style="display:flex;gap:8px;align-items:center;">
          <select name="status" style="font-size:14px;padding:6px 12px;">
            <?php foreach (['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'] as $st): ?>
              <option value="<?= $st ?>" <?= $order['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="update_status" value="1" class="button button-blue button-sm">Update Status</button>
        </form>
      </div>
    </div>

    <div class="grid g2" style="margin-bottom:24px;">
      <div style="background:var(--soft);padding:18px;border-radius:var(--r);">
        <h3 style="font-size:15px;margin-bottom:8px;">Customer Information</h3>
        <p style="font-size:14px;color:var(--ink);line-height:1.6;">
          <strong>Name:</strong> <?= e($order['customer_name']) ?><br>
          <strong>Email:</strong> <?= e($order['customer_email']) ?><br>
          <strong>Phone:</strong> <?= e($order['customer_phone']) ?>
        </p>
      </div>
      <div style="background:var(--soft);padding:18px;border-radius:var(--r);">
        <h3 style="font-size:15px;margin-bottom:8px;">Shipping & Payment</h3>
        <p style="font-size:14px;color:var(--ink);line-height:1.6;">
          <strong>Address:</strong><br><?= nl2br(e($order['shipping_address'])) ?><br>
          <strong>Payment Method:</strong> <?= e($order['payment_method']) ?>
          <?php if (!empty($order['notes'])): ?>
            <br><strong>Notes:</strong> <?= e($order['notes']) ?>
          <?php endif; ?>
        </p>
      </div>
    </div>

    <h3 style="font-size:16px;margin-bottom:12px;">Items Ordered</h3>
    <table class="admin-table" style="margin-bottom:24px;">
      <thead>
        <tr>
          <th>Item</th>
          <th>Unit Price</th>
          <th>Qty</th>
          <th style="text-align:right;">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:12px;">
                <?php if (!empty($it['image_url'])): ?>
                  <img src="<?= e($it['image_url']) ?>" alt="" style="width:40px;height:34px;object-fit:cover;border-radius:4px;">
                <?php endif; ?>
                <strong><?= e($it['product_name']) ?></strong>
              </div>
            </td>
            <td><?= formatPrice($it['price']) ?></td>
            <td><?= (int)$it['quantity'] ?></td>
            <td style="text-align:right;"><strong><?= formatPrice($it['total_price']) ?></strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div style="max-width:300px;margin-left:auto;">
      <div class="sum-line"><span>Subtotal:</span> <span><?= formatPrice($order['subtotal']) ?></span></div>
      <div class="sum-line"><span>Shipping Fee:</span> <span><?= $order['shipping_fee'] > 0 ? formatPrice($order['shipping_fee']) : 'Free' ?></span></div>
      <div class="sum-line total"><span>Grand Total:</span> <span><?= formatPrice($order['total_amount']) ?></span></div>
    </div>

    <div style="margin-top:32px;display:flex;gap:12px;justify-content:flex-end;">
      <button onclick="window.print()" class="button button-ghost button-sm">Print Invoice</button>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
