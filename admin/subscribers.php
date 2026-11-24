<?php
$adminTitle = 'Newsletter Subscribers';
$activeAdminNav = 'subscribers';

require_once __DIR__ . '/includes/header.php';

$message = '';
$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $subId = (int)($_GET['id'] ?? 0);
    if ($subId > 0) {
        $pdo->prepare("DELETE FROM subscribers WHERE id = ?")->execute([$subId]);
        $message = 'Subscriber removed.';
    }
}

$subscribers = $pdo->query("SELECT * FROM subscribers ORDER BY id DESC")->fetchAll();
?>

<div class="wrap" style="padding-top:32px;">
  <?php if (!empty($message)): ?>
    <div style="background:#def7ec;color:#03543f;padding:12px 18px;border-radius:var(--r);margin-bottom:20px;font-weight:600;">
      <?= e($message) ?>
    </div>
  <?php endif; ?>

  <div class="panel" style="max-width:760px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <div>
        <h2 style="font-size:22px;">Newsletter Subscribers</h2>
        <p style="color:var(--muted);font-size:14px;">Total of <?= count($subscribers) ?> subscribers in salespro database</p>
      </div>
    </div>

    <?php if (empty($subscribers)): ?>
      <p style="text-align:center;padding:40px 0;color:var(--muted);">No subscribers yet.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Email Address</th>
            <th>Subscribed Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subscribers as $idx => $s): ?>
            <tr>
              <td><?= $idx + 1 ?></td>
              <td><strong><?= e($s['email']) ?></strong></td>
              <td><?= date('M j, Y, g:i A', strtotime($s['created_at'])) ?></td>
              <td>
                <a href="subscribers.php?action=delete&id=<?= (int)$s['id'] ?>" onclick="return confirm('Remove subscriber?');" class="button button-sm" style="padding:4px 8px;min-height:28px;color:#e53e3e;border-color:#e53e3e;background:transparent;">Remove</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

