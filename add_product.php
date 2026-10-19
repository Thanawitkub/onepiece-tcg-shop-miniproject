<?php
// add_product.php - Admin: Insert New Product Form
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

// Authentication Check: Admin Only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php?admin_required=1');
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
        $error_msg = 'Please fill in all required fields with valid values. Price and stock must be positive.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, description, price, stock, image_url) VALUES (:name, :cat, :desc, :price, :stock, :img)");
        $success = $stmt->execute([
            ':name' => $name,
            ':cat' => $category,
            ':desc' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':img' => $image_url
        ]);

        if ($success) {
            header('Location: admin.php?added=1');
            exit;
        } else {
            $error_msg = 'Database insertion error. Could not save product.';
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
            <h1 class="section-title"><?php echo htmlspecialchars(__t('form_add_title')); ?></h1>
            <p class="section-subtitle">Add a new booster pack, box, or carton to the Grand Line inventory.</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <form action="add_product.php" method="POST">
                
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label for="name"><?php echo htmlspecialchars(__t('form_prod_name')); ?> *</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. One Piece Booster Pack OP-14" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.25rem;">
                    <div class="form-group">
                        <label for="category"><?php echo htmlspecialchars(__t('form_prod_cat')); ?> *</label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="Booster Pack">Booster Pack</option>
                            <option value="Booster Box">Booster Box</option>
                            <option value="Carton">Carton</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price"><?php echo htmlspecialchars(__t('form_prod_price')); ?> *</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="e.g. 95.00" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.25rem;">
                    <div class="form-group">
                        <label for="stock"><?php echo htmlspecialchars(__t('form_prod_stock')); ?> *</label>
                        <input type="number" name="stock" id="stock" class="form-control" placeholder="e.g. 50" min="0" required value="<?php echo htmlspecialchars($_POST['stock'] ?? '50'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="image_url"><?php echo htmlspecialchars(__t('form_prod_image')); ?> *</label>
                        <input type="text" name="image_url" id="image_url" class="form-control" placeholder="images/pack/pack_op01.png" required value="<?php echo htmlspecialchars($_POST['image_url'] ?? 'images/pack/pack_op01.png'); ?>">
                    </div>
                </div>

                <!-- Quick Image Preset Buttons -->
                <div style="margin-bottom: 1.25rem; background: rgba(5, 12, 25, 0.5); padding: 0.8rem; border-radius: 8px; font-size: 0.8rem;">
                    <span style="color: var(--text-muted); display: block; margin-bottom: 0.4rem;">Quick image templates:</span>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button type="button" class="btn-ghost btn-sm" onclick="document.getElementById('image_url').value='images/pack/pack_op01.png'">Pack OP-01</button>
                        <button type="button" class="btn-ghost btn-sm" onclick="document.getElementById('image_url').value='images/pack/pack_op05.png'">Pack OP-05</button>
                        <button type="button" class="btn-ghost btn-sm" onclick="document.getElementById('image_url').value='images/box/box_op01.png'">Box OP-01</button>
                        <button type="button" class="btn-ghost btn-sm" onclick="document.getElementById('image_url').value='images/box/box_op05.png'">Box OP-05</button>
                        <button type="button" class="btn-ghost btn-sm" onclick="document.getElementById('image_url').value='images/box/box_op09.png'">Box OP-09</button>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.75rem;">
                    <label for="description"><?php echo htmlspecialchars(__t('form_prod_desc')); ?></label>
                    <textarea name="description" id="description" rows="4" class="form-control" placeholder="Provide product details, cards per pack, rarities, or lore..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn-haki" style="flex: 1; justify-content: center;">
                        <i class="fa-solid fa-floppy-disk"></i> <?php echo htmlspecialchars(__t('form_btn_save')); ?>
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

