<?php
$pageTitle = 'Contact | SalesPro';
$pageDescription = 'Contact SalesPro customer service.';
$activeNav = 'contact';

require_once __DIR__ . '/config/db.php';
$pdo = getDbConnection();

$success = false;
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $topic = trim($_POST['topic'] ?? 'General inquiry');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill out all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, topic, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $topic, $message]);
            $success = true;
        } catch (Exception $e) {
            $error = 'An error occurred while saving your message. Please try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

  <main id="main">
    <section class="page-head">
      <div class="wrap">
        <p class="crumbs"><a href="index.php">Home</a> / Contact</p>
        <h1>Contact us</h1>
        <p>Questions about a product or an order? We usually reply within one business day.</p>
      </div>
    </section>

    <section class="section" style="padding-top:36px">
      <div class="wrap split">
        <div class="panel">
          <?php if ($success): ?>
            <div class="notice" style="display:block;">
              Thank you, <strong><?= e($name) ?></strong>! Your message has been received. Our team will get back to you shortly at <?= e($email) ?>.
            </div>
          <?php endif; ?>

          <?php if (!empty($error)): ?>
            <div style="background:#fde8e8;color:#9b1c1c;padding:14px 18px;border-radius:var(--r);margin-bottom:18px;font-weight:600;">
              <?= e($error) ?>
            </div>
          <?php endif; ?>

          <form class="form" method="POST" action="contact.php" data-validate>
            <div class="field">
              <label for="name">Full name *</label>
              <input id="name" name="name" type="text" required autocomplete="name" value="<?= $success ? '' : (isset($_POST['name']) ? e($_POST['name']) : '') ?>">
              <span class="err"></span>
            </div>
            <div class="field">
              <label for="email">Email *</label>
              <input id="email" name="email" type="email" required autocomplete="email" value="<?= $success ? '' : (isset($_POST['email']) ? e($_POST['email']) : '') ?>">
              <span class="err"></span>
            </div>
            <div class="field">
              <label for="topic">Topic</label>
              <select id="topic" name="topic">
                <option value="Product question">Product question</option>
                <option value="Order help">Order help</option>
                <option value="Warranty">Warranty</option>
                <option value="Other">Other</option>
              </select>
              <span class="err"></span>
            </div>
            <div class="field">
              <label for="msg">Message *</label>
              <textarea id="msg" name="message" rows="5" required placeholder="How can we help you?"><?= $success ? '' : (isset($_POST['message']) ? e($_POST['message']) : '') ?></textarea>
              <span class="err"></span>
            </div>
            <button class="button button-blue" type="submit">Send message</button>
          </form>
        </div>

        <aside>
          <h2 style="font-size:22px;margin-bottom:12px">Get in touch</h2>
          <p style="color:var(--muted);margin-bottom:10px">
            <strong style="color:var(--ink)">Email:</strong> support@salespro.example
          </p>
          <p style="color:var(--muted);margin-bottom:10px">
            <strong style="color:var(--ink)">Phone:</strong> +880 1234 567890
          </p>
          <p style="color:var(--muted);margin-bottom:20px">
            <strong style="color:var(--ink)">Hours:</strong> Sat–Thu, 9am–7pm
          </p>

          <div class="panel" style="background:var(--soft);">
            <h3 style="font-size:16px;margin-bottom:8px;">Fast Assistance</h3>
            <p style="color:var(--muted);font-size:14px;margin-bottom:10px;">Need quick help with order tracking or warranty claims?</p>
            <a href="support.php" class="text-link">Visit our Help Center <span>→</span></a>
          </div>
        </aside>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
