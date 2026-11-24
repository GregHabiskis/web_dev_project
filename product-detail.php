<?php
require_once __DIR__ . '/config/db.php';
$pdo = getDbConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug 
                       FROM products p 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

$pageTitle = $product['name'] . ' | SalesPro';
$pageDescription = $product['description'];
$activeNav = 'products';

// Related products
$relStmt = $pdo->prepare("SELECT p.*, c.name AS category_name 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.category_id = ? AND p.id != ? 
                          LIMIT 4");
$relStmt->execute([$product['category_id'], $product['id']]);
$related = $relStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

  <main id="main">
    <section class="page-head">
      <div class="wrap">
        <p class="crumbs">
          <a href="index.php">Home</a> / 
          <a href="products.php">Products</a> / 
          <a href="products.php?cat=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a> / 
          <?= e($product['name']) ?>
        </p>
        <h1><?= e($product['name']) ?></h1>
      </div>
    </section>

    <section class="section" style="padding-top:40px">
      <div class="wrap split">
        <div>
          <div style="background:var(--soft);border-radius:var(--r);overflow:hidden;border:1px solid var(--line);max-height:480px;display:flex;align-items:center;justify-content:center;">
            <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" style="max-height:450px;width:100%;object-fit:cover;">
          </div>
        </div>
        <div>
          <div class="panel">
            <span class="tag" style="margin:0 0 12px 0;"><?= e($product['category_name']) ?></span>
            <h2 style="font-size:28px;margin-bottom:12px;"><?= e($product['name']) ?></h2>
            <div style="font-size:28px;font-weight:700;color:var(--ink);margin-bottom:18px;">
              <?= formatPrice($product['price']) ?>
            </div>
            <p style="color:var(--muted);font-size:16px;line-height:1.6;margin-bottom:24px;">
              <?= nl2br(e($product['description'])) ?>
            </p>

            <div style="margin-bottom:24px;padding:16px;background:var(--soft);border-radius:var(--r);font-size:14px;">
              <div style="margin-bottom:6px;"><strong>Availability:</strong> <?= $product['stock'] > 0 ? '<span style="color:#17612b;">In Stock (' . (int)$product['stock'] . ' available)</span>' : '<span style="color:#b3261e;">Out of Stock</span>' ?></div>
              <div style="margin-bottom:6px;"><strong>Warranty:</strong> 3-Year Official SalesPro Warranty</div>
              <div><strong>Delivery:</strong> Free delivery on orders over $500</div>
            </div>

            <div style="display:flex;gap:12px;align-items:center;">
              <button class="button button-blue" data-add="<?= (int)$product['id'] ?>" style="flex:1;">
                Add to Cart
              </button>
              <a href="cart.php" class="button button-ghost">Go to Cart</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php if (!empty($related)): ?>
      <section class="section alt">
        <div class="wrap">
          <div class="section-heading">
            <h2>Related Products</h2>
            <p>You might also be interested in these items.</p>
          </div>
          <div class="grid g4">
            <?php foreach ($related as $rel): ?>
              <article class="product-card">
                <div class="product-image">
                  <a href="product-detail.php?id=<?= (int)$rel['id'] ?>">
                    <img loading="lazy" src="<?= e($rel['image_url']) ?>" alt="<?= e($rel['name']) ?>">
                  </a>
                </div>
                <span class="tag"><?= e($rel['category_name']) ?></span>
                <h3>
                  <a href="product-detail.php?id=<?= (int)$rel['id'] ?>" style="color:inherit;"><?= e($rel['name']) ?></a>
                </h3>
                <p><?= e($rel['description']) ?></p>
                <div class="price-row">
                  <span class="price"><?= formatPrice($rel['price']) ?></span>
                  <button class="button button-dark button-sm" data-add="<?= (int)$rel['id'] ?>">Add to cart</button>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

