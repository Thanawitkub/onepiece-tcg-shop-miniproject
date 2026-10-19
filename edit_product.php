<?php
// edit_product.php - Admin: Update Existing Product Form
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

// Authentication Check: Admin Only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php?admin_required=1');
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: admin.php?error=invalid_id');
    exit;
}

// Fetch current product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: admin.php?error=not_found');
    exit;
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT);
    $image_url = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $allowed_categories = ['Booster Pack', 'Booster Box', 'Carton'];

    if (empty($name) || !in_array($category, $allowed_categories) || $price === false || $price <= 0 || $stock === false || $stock < 0 || empty($image_url)) {
        $error_msg = 'Please fill in all required fields with valid values. Price and stock must be positive numbers.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE products SET name = :name, category = :cat, description = :desc, price = :price, stock = :stock, image_url = :img WHERE id = :id");
        $success = $update_stmt->execute([
            ':name' => $name,
            ':cat' => $category,
            ':desc' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':img' => $image_url,
            ':id' => $id
        ]);

        if ($success) {
            header('Location: admin.php?updated=1');
            exit;
        } else {
            $error_msg = 'Failed to update product in database.';
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<main class="main-container">
    <div class="form-container">
        
        <div style="margin-bottom: 2rem;">
            <a href="admin.php" class="btn-ghost btn-sm" style="margin-bottom: 1rem;">
                <i class="fa-solid fa-arrow-left"></i> Return to Product List
            </a>
            <h1 class="section-title"><?php echo htmlspecialchars(__t('form_edit_title')); ?></h1>
            <p class="section-subtitle">Editing #<?php echo $product['id']; ?>: <?php echo htmlspecialchars($product['name']); ?></p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <form action="edit_product.php?id=<?php echo $id; ?>" method="POST">
                
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label for="name"><?php echo htmlspecialchars(__t('form_prod_name')); ?> *</label>
                    <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.25rem;">
                    <div class="form-group">
                        <label for="category"><?php echo htmlspecialchars(__t('form_prod_cat')); ?> *</label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="Booster Pack" <?php echo ($product['category'] === 'Booster Pack') ? 'selected' : ''; ?>>Booster Pack</option>
                            <option value="Booster Box" <?php echo ($product['category'] === 'Booster Box') ? 'selected' : ''; ?>>Booster Box</option>
                            <option value="Carton" <?php echo ($product['category'] === 'Carton') ? 'selected' : ''; ?>>Carton</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price"><?php echo htmlspecialchars(__t('form_prod_price')); ?> *</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" required value="<?php echo htmlspecialchars($product['price']); ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.25rem;">
                    <div class="form-group">
                        <label for="stock"><?php echo htmlspecialchars(__t('form_prod_stock')); ?> *</label>
                        <input type="number" name="stock" id="stock" class="form-control" min="0" required value="<?php echo htmlspecialchars($product['stock']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="image_url"><?php echo htmlspecialchars(__t('form_prod_image')); ?> *</label>
                        <input type="text" name="image_url" id="image_url" class="form-control" required value="<?php echo htmlspecialchars($product['image_url']); ?>">
                    </div>
                </div>

                <!-- Current Image Preview -->
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; background: rgba(5,12,25,0.6); padding: 0.8rem; border-radius: 8px;">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Card" style="height: 60px; object-fit: contain;">
                    <span style="font-size: 0.82rem; color: var(--text-muted);">Current image preview: <code><?php echo htmlspecialchars($product['image_url']); ?></code></span>
                </div>

                <div class="form-group" style="margin-bottom: 1.75rem;">
                    <label for="description"><?php echo htmlspecialchars(__t('form_prod_desc')); ?></label>
                    <textarea name="description" id="description" rows="4" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn-haki" style="flex: 1; justify-content: center;">
                        <i class="fa-solid fa-arrows-rotate"></i> <?php echo htmlspecialchars(__t('form_btn_update')); ?>
                    </button>
                    <a href="admin.php" class="btn-ghost" style="justify-content: center;">
                        <?php echo htmlspecialchars(__t('form_btn_cancel')); ?>
                    </a>
                </div>

            </form>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>

