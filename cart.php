<?php
$pageTitle = 'Shopping Cart | SalesPro';
$pageDescription = 'Review your cart items and complete checkout at SalesPro.';
$activeNav = '';

require_once __DIR__ . '/config/db.php';
$pdo = getDbConnection();

$error = '';
$cartData = getCartDetails($pdo);

// Handle Checkout Submission
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['place_order'])) {
    if (empty($cartData['items'])) {
        $error = 'Your cart is empty. Please add items before placing an order.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? 'Cash on Delivery');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $error = 'Please fill in all required shipping fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email address.';
        } else {
            try {
                $pdo->beginTransaction();

                // Unique Order Number
                $orderNumber = 'SP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

                // Insert into orders table
                $stmt = $pdo->prepare("INSERT INTO orders 
                    (order_number, customer_name, customer_email, customer_phone, shipping_address, payment_method, subtotal, shipping_fee, total_amount, status, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)");
                $stmt->execute([
                    $orderNumber,
                    $name,
                    $email,
                    $phone,
                    $address,
                    $paymentMethod,
                    $cartData['subtotal'],
                    $cartData['shipping'],
                    $cartData['total'],
                    $notes
                ]);
                $orderId = $pdo->lastInsertId();

                // Insert order items
                $itemStmt = $pdo->prepare("INSERT INTO order_items 
                    (order_id, product_id, product_name, price, quantity, total_price) 
                    VALUES (?, ?, ?, ?, ?, ?)");

                // Optionally reduce stock
                $stockStmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");

                foreach ($cartData['items'] as $item) {
                    $itemStmt->execute([
                        $orderId,
                        $item['id'],
                        $item['name'],
                        $item['price'],
                        $item['quantity'],
                        $item['line_total']
                    ]);
                    $stockStmt->execute([$item['quantity'], $item['id']]);
                }

                $pdo->commit();

                // Clear cart in session
                $_SESSION['cart'] = [];

                // Redirect to success page
                header("Location: order-success.php?order=" . urlencode($orderNumber));
                exit;

            } catch (Exception $ex) {
                $pdo->rollBack();
                $error = 'Failed to process order: ' . $ex->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

  <main id="main">
    <section class="page-head">
      <div class="wrap">
        <p class="crumbs"><a href="index.php">Home</a> / Cart</p>
        <h1>Your cart</h1>
        <p>Review your items and complete your order.</p>
      </div>
    </section>

    <section class="section" style="padding-top:36px">
      <div class="wrap">
        <?php if (!empty($error)): ?>
          <div style="background:#fde8e8;color:#9b1c1c;padding:14px 18px;border-radius:var(--r);margin-bottom:24px;font-weight:600;">
            <?= e($error) ?>
          </div>
        <?php endif; ?>

        <div class="split" id="cartContainer">
          <!-- Cart Items List -->
          <div>
            <div id="cartList">
              <?php if (empty($cartData['items'])): ?>
                <div class="panel" style="text-align:center;padding:50px 20px;">
                  <svg viewBox="0 0 24 24" style="width:60px;height:60px;stroke:var(--muted);fill:none;margin-bottom:16px;">
                    <path d="M3 4h2l2.5 11h10L20 7H6.5"/><circle cx="9" cy="19" r="1.2"/><circle cx="17" cy="19" r="1.2"/>
                  </svg>
                  <p class="empty" style="padding:0 0 16px;">Your cart is currently empty.</p>
                  <a class="button button-dark" href="products.php">Browse products</a>
                </div>
              <?php else: ?>
                <?php foreach ($cartData['items'] as $item): ?>
                  <div class="cart-row" data-item-id="<?= (int)$item['id'] ?>">
                    <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>">
                    <div>
                      <strong><a href="product-detail.php?id=<?= (int)$item['id'] ?>" style="color:inherit;"><?= e($item['name']) ?></a></strong>
                      <div style="color:var(--muted);font-size:13px;margin:2px 0;"><?= formatPrice($item['price']) ?> each</div>
                      <div class="qty">
                        <button type="button" data-q="-1" data-id="<?= (int)$item['id'] ?>" aria-label="Decrease quantity">−</button>
                        <span><?= (int)$item['quantity'] ?></span>
                        <button type="button" data-q="1" data-id="<?= (int)$item['id'] ?>" aria-label="Increase quantity">+</button>
                      </div>
                      <button type="button" class="link-btn" data-rm="<?= (int)$item['id'] ?>">Remove</button>
                    </div>
                    <div>
                      <strong><?= formatPrice($item['line_total']) ?></strong>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <!-- Checkout Form (Visible when cart has items) -->
            <?php if (!empty($cartData['items'])): ?>
              <div class="panel" style="margin-top:32px;">
                <h2 style="font-size:22px;margin-bottom:16px;">Shipping & Customer Information</h2>
                <form method="POST" action="cart.php" class="form" id="checkoutForm" data-validate>
                  <div class="grid g2">
                    <div class="field">
                      <label for="name">Full Name *</label>
                      <input id="name" name="name" type="text" required placeholder="John Doe" value="<?= isset($_POST['name']) ? e($_POST['name']) : '' ?>">
                      <span class="err"></span>
                    </div>
                    <div class="field">
                      <label for="email">Email Address *</label>
                      <input id="email" name="email" type="email" required placeholder="john@example.com" value="<?= isset($_POST['email']) ? e($_POST['email']) : '' ?>">
                      <span class="err"></span>
                    </div>
                  </div>

                  <div class="grid g2">
                    <div class="field">
                      <label for="phone">Phone Number *</label>
                      <input id="phone" name="phone" type="tel" required placeholder="+880 1XXXXXXXXX" value="<?= isset($_POST['phone']) ? e($_POST['phone']) : '' ?>">
                      <span class="err"></span>
                    </div>
                    <div class="field">
                      <label for="payment_method">Payment Method</label>
                      <select id="payment_method" name="payment_method">
                        <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                        <option value="Bank Transfer">Direct Bank Transfer</option>
                        <option value="Mobile Payment">bKash / Nagad / Rocket</option>
                        <option value="Credit / Debit Card">Credit / Debit Card</option>
                      </select>
                      <span class="err"></span>
                    </div>
                  </div>

                  <div class="field">
                    <label for="address">Delivery Street Address *</label>
                    <textarea id="address" name="address" rows="3" required placeholder="Full street address, city, postal code"><?= isset($_POST['address']) ? e($_POST['address']) : '' ?></textarea>
                    <span class="err"></span>
                  </div>

                  <div class="field">
                    <label for="notes">Order Notes (Optional)</label>
                    <input id="notes" name="notes" type="text" placeholder="Special delivery instructions, preferred delivery time, etc." value="<?= isset($_POST['notes']) ? e($_POST['notes']) : '' ?>">
                  </div>

                  <button type="submit" name="place_order" value="1" class="button button-blue" style="width:100%;margin-top:10px;font-size:16px;">
                    Place Order (<?= formatPrice($cartData['total']) ?>)
                  </button>
                </form>
              </div>
            <?php endif; ?>
          </div>

          <!-- Order Summary Panel -->
          <aside class="panel" id="cartSummary" <?= empty($cartData['items']) ? 'hidden' : '' ?>>
            <h2 style="font-size:20px;margin-bottom:14px">Order summary</h2>
            <div class="sum-line">
              <span>Subtotal</span>
              <span id="sub"><?= formatPrice($cartData['subtotal']) ?></span>
            </div>
            <div class="sum-line">
              <span>Shipping</span>
              <span id="ship"><?= $cartData['shipping'] > 0 ? formatPrice($cartData['shipping']) : 'Free' ?></span>
            </div>
            <div class="sum-line total">
              <span>Total</span>
              <span id="tot"><?= formatPrice($cartData['total']) ?></span>
            </div>
            <p style="font-size:12px;color:var(--muted);margin-top:14px">
              Free shipping on orders over $500.<br>All taxes included.
            </p>
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--line);font-size:13px;color:var(--muted);">
              <div>🔒 Safe & Secure Checkout</div>
              <div style="margin-top:6px;">📦 30-Day Return Guarantee</div>
              <div style="margin-top:6px;">🛡️ 3-Year Warranty on PCs</div>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
