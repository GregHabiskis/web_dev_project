<?php
$pageTitle = 'Products | SalesPro';
$pageDescription = 'Browse laptops, desktops, printers, monitors and accessories at SalesPro.';
$activeNav = 'products';

require_once __DIR__ . '/config/db.php';
$pdo = getDbConnection();

// Fetch all categories for filter chips
$catsStmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $catsStmt->fetchAll();

// Get filter parameters
$catFilter = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$sortOption = isset($_GET['sort']) ? trim($_GET['sort']) : 'featured';

// Build SQL query dynamically with safe prepared statements
$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if ($catFilter !== 'all' && !empty($catFilter)) {
    $sql .= " AND c.slug = ?";
    $params[] = $catFilter;
}

if (!empty($searchQuery)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
}

switch ($sortOption) {
    case 'low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $sql .= " ORDER BY p.name ASC";
        break;
    case 'featured':
    default:
        $sql .= " ORDER BY p.is_featured DESC, p.id ASC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

  <main id="main">
    <section class="page-head">
      <div class="wrap">
        <p class="crumbs"><a href="index.php">Home</a> / Products</p>
        <h1>All products</h1>
        <p>Browse laptops, desktops, printers, monitors and accessories.</p>
      </div>
    </section>

    <section class="section" style="padding-top:36px">
      <div class="wrap">
        <!-- Toolbar with Filter Chips, Search, & Sort -->
        <div class="toolbar">
          <div class="chips" id="chips" role="group" aria-label="Filter by category">
            <a href="products.php?cat=all<?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?><?= $sortOption !== 'featured' ? '&sort=' . urlencode($sortOption) : '' ?>" 
               class="chip <?= ($catFilter === 'all' || empty($catFilter)) ? 'active' : '' ?>" data-cat="all">All</a>
            <?php foreach ($categories as $cat): ?>
              <a href="products.php?cat=<?= e($cat['slug']) ?><?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?><?= $sortOption !== 'featured' ? '&sort=' . urlencode($sortOption) : '' ?>" 
                 class="chip <?= $catFilter === $cat['slug'] ? 'active' : '' ?>" data-cat="<?= e($cat['slug']) ?>">
                <?= e($cat['name']) ?>
              </a>
            <?php endforeach; ?>
          </div>

          <form method="GET" action="products.php" class="tools" id="filterForm">
            <?php if ($catFilter !== 'all'): ?>
              <input type="hidden" name="cat" value="<?= e($catFilter) ?>">
            <?php endif; ?>
            <input type="search" name="q" id="search" placeholder="Search products" aria-label="Search products" value="<?= e($searchQuery) ?>">
            <select name="sort" id="sort" aria-label="Sort products" onchange="document.getElementById('filterForm').submit();">
              <option value="featured" <?= $sortOption === 'featured' ? 'selected' : '' ?>>Featured</option>
              <option value="low" <?= $sortOption === 'low' ? 'selected' : '' ?>>Price: low to high</option>
              <option value="high" <?= $sortOption === 'high' ? 'selected' : '' ?>>Price: high to low</option>
              <option value="name" <?= $sortOption === 'name' ? 'selected' : '' ?>>Name: A to Z</option>
            </select>
          </form>
        </div>

        <p id="resultCount" aria-live="polite" style="color:var(--muted);font-size:14px;margin-bottom:18px">
          <?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?> found
        </p>

        <!-- Product Grid -->
        <div class="grid g4" id="productGrid">
          <?php if (empty($products)): ?>
            <p class="empty">No products match your search. Try another keyword or category.</p>
          <?php else: ?>
            <?php foreach ($products as $p): ?>
              <article class="product-card" data-cat="<?= e($p['category_slug']) ?>" data-name="<?= strtolower(e($p['name'])) ?>" data-price="<?= (float)$p['price'] ?>">
                <div class="product-image">
                  <a href="product-detail.php?id=<?= (int)$p['id'] ?>">
                    <img loading="lazy" src="<?= e($p['image_url']) ?>" alt="<?= e($p['name']) ?>">
                  </a>
                </div>
                <span class="tag"><?= e($p['category_name']) ?></span>
                <h3>
                  <a href="product-detail.php?id=<?= (int)$p['id'] ?>" style="color:inherit;"><?= e($p['name']) ?></a>
                </h3>
                <p><?= e($p['description']) ?></p>
                <div class="price-row">
                  <span class="price"><?= formatPrice($p['price']) ?></span>
                  <button class="button button-dark button-sm" data-add="<?= (int)$p['id'] ?>">Add to cart</button>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

