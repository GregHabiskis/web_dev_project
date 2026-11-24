<?php
$adminTitle = 'Product Management';
$activeAdminNav = 'products';

require_once __DIR__ . '/includes/header.php';

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch categories for select dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Handle Delete
if ($action === 'delete' && $editId > 0) {
    try {
        $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delStmt->execute([$editId]);
        $message = 'Product successfully deleted.';
        $action = 'list';
    } catch (Exception $e) {
        $error = 'Could not delete product: ' . $e->getMessage();
    }
}

// Handle Add / Edit POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_product'])) {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $stock = (int)($_POST['stock'] ?? 50);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

    if (empty($name) || $categoryId <= 0 || $price < 0) {
        $error = 'Please fill out all required fields with valid values.';
    } else {
        try {
            if ($editId > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE products SET 
                    category_id = ?, name = ?, slug = ?, price = ?, description = ?, image_url = ?, is_featured = ?, stock = ? 
                    WHERE id = ?");
                $stmt->execute([$categoryId, $name, $slug, $price, $description, $imageUrl, $isFeatured, $stock, $editId]);
                $message = 'Product updated successfully.';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO products 
                    (category_id, name, slug, price, description, image_url, is_featured, stock) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$categoryId, $name, $slug, $price, $description, $imageUrl, $isFeatured, $stock]);
                $message = 'New product created successfully.';
            }
            $action = 'list';
        } catch (Exception $e) {
            $error = 'Error saving product: ' . $e->getMessage();
        }
    }
}

// Product to edit if editing
$editProduct = null;
if ($action === 'edit' && $editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
    if (!$editProduct) {
        $error = 'Product not found.';
        $action = 'list';
    }
}

// Fetch all products for listing
$productsList = $pdo->query("SELECT p.*, c.name AS category_name 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.id 
                             ORDER BY p.id DESC")->fetchAll();
?>

<div class="wrap" style="padding-top:32px;">
  <?php if (!empty($message)): ?>
    <div style="background:#def7ec;color:#03543f;padding:12px 18px;border-radius:var(--r);margin-bottom:20px;font-weight:600;">
      <?= e($message) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div style="background:#fde8e8;color:#9b1c1c;padding:12px 18px;border-radius:var(--r);margin-bottom:20px;font-weight:600;">
      <?= e($error) ?>
    </div>
  <?php endif; ?>

  <?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Add / Edit Product Form -->
    <div class="panel" style="max-width:760px;margin:0 auto 40px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2 style="font-size:22px;"><?= $action === 'edit' ? 'Edit Product' : 'Add New Product' ?></h2>
        <a href="products.php" class="button button-ghost button-sm">← Back to List</a>
      </div>

      <form method="POST" action="products.php?action=<?= $action ?><?= $editId > 0 ? '&id=' . $editId : '' ?>" class="form">
        <div class="field">
          <label for="name">Product Name *</label>
          <input type="text" id="name" name="name" required value="<?= e($editProduct['name'] ?? '') ?>" placeholder="e.g. SalesPro Book 16">
        </div>

        <div class="grid g2">
          <div class="field">
            <label for="category_id">Category *</label>
            <select id="category_id" name="category_id" required>
              <option value="">Select Category</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= (isset($editProduct['category_id']) && $editProduct['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label for="price">Price ($) *</label>
            <input type="number" step="0.01" min="0" id="price" name="price" required value="<?= e($editProduct['price'] ?? '') ?>" placeholder="999.00">
          </div>
        </div>

        <div class="grid g2">
          <div class="field">
            <label for="stock">Stock Quantity</label>
            <input type="number" min="0" id="stock" name="stock" value="<?= e($editProduct['stock'] ?? 50) ?>">
          </div>
          <div class="field" style="display:flex;align-items:center;gap:10px;padding-top:28px;">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= (!empty($editProduct['is_featured'])) ? 'checked' : '' ?> style="width:18px;height:18px;">
            <label for="is_featured" style="cursor:pointer;">Feature on Homepage (Popular right now)</label>
          </div>
        </div>

        <div class="field">
          <label for="image_url">Product Image URL</label>
          <input type="url" id="image_url" name="image_url" value="<?= e($editProduct['image_url'] ?? '') ?>" placeholder="https://images.unsplash.com/...">
        </div>

        <div class="field">
          <label for="description">Product Description</label>
          <textarea id="description" name="description" rows="4" placeholder="Features, specifications, and details"><?= e($editProduct['description'] ?? '') ?></textarea>
        </div>

        <div style="display:flex;gap:12px;margin-top:10px;">
          <button type="submit" name="save_product" value="1" class="button button-blue">
            <?= $action === 'edit' ? 'Update Product' : 'Create Product' ?>
          </button>
          <a href="products.php" class="button button-ghost">Cancel</a>
        </div>
      </form>
    </div>
  <?php else: ?>
    <!-- Products Table Listing -->
    <div class="panel">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:14px;">
        <div>
          <h2 style="font-size:22px;">Product Catalog</h2>
          <p style="color:var(--muted);font-size:14px;">Total of <?= count($productsList) ?> products in salespro database</p>
        </div>
        <a href="products.php?action=add" class="button button-blue button-sm">+ Add New Product</a>
      </div>

      <div style="overflow-x:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Image</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Featured</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($productsList as $p): ?>
              <tr>
                <td style="width:60px;">
                  <img src="<?= e($p['image_url']) ?>" alt="" style="width:48px;height:40px;object-fit:cover;border-radius:4px;">
                </td>
                <td>
                  <strong><?= e($p['name']) ?></strong>
                  <div style="font-size:12px;color:var(--muted);"><?= e(substr($p['description'], 0, 45)) ?>...</div>
                </td>
                <td><span class="tag" style="margin:0;"><?= e($p['category_name']) ?></span></td>
                <td><strong><?= formatPrice($p['price']) ?></strong></td>
                <td><?= (int)$p['stock'] ?> in stock</td>
                <td>
                  <?= $p['is_featured'] ? '<span style="color:#10b981;font-weight:700;">★ Yes</span>' : '<span style="color:var(--muted);">No</span>' ?>
                </td>
                <td style="white-space:nowrap;">
                  <a href="products.php?action=edit&id=<?= (int)$p['id'] ?>" class="button button-ghost button-sm" style="padding:4px 8px;min-height:28px;">Edit</a>
                  <a href="products.php?action=delete&id=<?= (int)$p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="button button-sm" style="padding:4px 8px;min-height:28px;color:#e53e3e;border-color:#e53e3e;background:transparent;">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
