<?php
$adminTitle = 'Contact Messages';
$activeAdminNav = 'messages';

require_once __DIR__ . '/includes/header.php';

$message = '';
$error = '';

// Handle actions
if (isset($_GET['action'])) {
    $msgId = (int)($_GET['id'] ?? 0);
    if ($_GET['action'] === 'read' && $msgId > 0) {
        $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$msgId]);
        $message = 'Message marked as read.';
    } elseif ($_GET['action'] === 'unread' && $msgId > 0) {
        $pdo->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?")->execute([$msgId]);
        $message = 'Message marked as unread.';
    } elseif ($_GET['action'] === 'delete' && $msgId > 0) {
        $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$msgId]);
        $message = 'Message deleted.';
    }
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
?>

<div class="wrap" style="padding-top:32px;">
  <?php if (!empty($message)): ?>
    <div style="background:#def7ec;color:#03543f;padding:12px 18px;border-radius:var(--r);margin-bottom:20px;font-weight:600;">
      <?= e($message) ?>
    </div>
  <?php endif; ?>

  <div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <div>
        <h2 style="font-size:22px;">Customer Inquiries</h2>
        <p style="color:var(--muted);font-size:14px;">Total of <?= count($messages) ?> inquiries in salespro database</p>
      </div>
    </div>

    <?php if (empty($messages)): ?>
      <p style="text-align:center;padding:40px 0;color:var(--muted);">No contact messages found.</p>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Status</th>
              <th>Date</th>
              <th>Customer</th>
              <th>Email</th>
              <th>Topic</th>
              <th>Message</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($messages as $m): ?>
              <tr style="<?= !$m['is_read'] ? 'background:#f0f9ff;font-weight:600;' : '' ?>">
                <td>
                  <?= $m['is_read'] ? '<span style="color:#10b981;font-size:12px;">✓ Read</span>' : '<span style="color:#ef4444;font-size:12px;font-weight:700;">● Unread</span>' ?>
                </td>
                <td><?= date('M j, Y', strtotime($m['created_at'])) ?></td>
                <td><?= e($m['name']) ?></td>
                <td><a href="mailto:<?= e($m['email']) ?>" style="color:var(--blue-d);"><?= e($m['email']) ?></a></td>
                <td><span class="tag" style="margin:0;"><?= e($m['topic']) ?></span></td>
                <td style="max-width:340px;line-height:1.4;font-size:13px;"><?= nl2br(e($m['message'])) ?></td>
                <td style="white-space:nowrap;">
                  <?php if (!$m['is_read']): ?>
                    <a href="messages.php?action=read&id=<?= (int)$m['id'] ?>" class="button button-ghost button-sm" style="padding:4px 8px;min-height:28px;">Mark Read</a>
                  <?php else: ?>
                    <a href="messages.php?action=unread&id=<?= (int)$m['id'] ?>" class="button button-ghost button-sm" style="padding:4px 8px;min-height:28px;">Mark Unread</a>
                  <?php endif; ?>
                  <a href="messages.php?action=delete&id=<?= (int)$m['id'] ?>" onclick="return confirm('Delete this message?');" class="button button-sm" style="padding:4px 8px;min-height:28px;color:#e53e3e;border-color:#e53e3e;background:transparent;">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

