<?php
$pageTitle = 'Support & FAQs | SalesPro';
$pageDescription = 'Find answers to common questions about orders, warranties, shipping and returns.';
$activeNav = 'support';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
?>

  <main id="main">
    <section class="page-head">
      <div class="wrap">
        <p class="crumbs"><a href="index.php">Home</a> / Support</p>
        <h1>Support</h1>
        <p>Find quick answers or talk to our team.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="grid g3">
          <div class="feature">
            <div class="ico">📦</div>
            <h3>Track an order</h3>
            <p>Use the order number from your confirmation receipt to check the status with our support desk.</p>
          </div>
          <div class="feature">
            <div class="ico">🔧</div>
            <h3>Repairs &amp; warranty</h3>
            <p>All computers and monitors come with full manufacturer warranty. Contact us to initiate repair service.</p>
          </div>
          <div class="feature">
            <div class="ico">💬</div>
            <h3>Contact us</h3>
            <p>Prefer to talk? <a class="text-link" style="color:var(--blue-d)" href="contact.php">Send us a message</a> or call our team.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section alt" id="faq">
      <div class="wrap" style="max-width:820px">
        <div class="section-heading">
          <h2>Frequently asked questions</h2>
        </div>
        <details>
          <summary>How long does delivery take?</summary>
          <p>Most orders arrive within 3–5 business days. You will receive updates by phone or email once your package ships.</p>
        </details>
        <details>
          <summary>What is your return policy?</summary>
          <p>You can return any product within 30 days of delivery in its original condition and packaging for a full refund or exchange.</p>
        </details>
        <details>
          <summary>How do I claim a warranty repair?</summary>
          <p>Contact us through our <a href="contact.php" style="color:var(--blue-d);text-decoration:underline;">contact form</a> with your order number and description of the issue. We will arrange a repair or replacement.</p>
        </details>
        <details>
          <summary>Can I change or cancel my order?</summary>
          <p>Yes, orders can be changed or canceled within 2 hours of placing them. After that, please call support immediately.</p>
        </details>
        <details>
          <summary>Which payment methods do you accept?</summary>
          <p>We accept Cash on Delivery (COD), Mobile Banking (bKash/Nagad), Direct Bank Wire, and major credit or debit cards.</p>
        </details>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

