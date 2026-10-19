<?php
// admin.php - Admin Product Management Dashboard
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

// Authentication Check: Admin Only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    // If not logged in as admin, redirect to login with notification
    header('Location: login.php?admin_required=1');
    exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>

<main class="main-container">

    <!-- Header & Action Row -->
    <div class="section-header-wrap">
        <div>
            <span class="haki-badge"><i class="fa-solid fa-crown"></i> Marine Headquarters Command</span>
            <h1 class="section-title" style="margin-top: 0.5rem; display: block;">
                <?php echo htmlspecialchars(__t('admin_title')); ?>
            </h1>
            <p class="section-subtitle">
                Manage, add, update, and remove booster packs, boxes, and cartons in the store inventory.
            </p>
        </div>

        <div style="display: flex; gap: 0.85rem; flex-wrap: wrap;">
            <a href="add_product.php" class="btn-haki">
                <i class="fa-solid fa-plus"></i> <?php echo htmlspecialchars(__t('admin_add_btn')); ?>
            </a>
            <a href="report_sales.php" class="btn-ghost">
                <i class="fa-solid fa-sack-dollar"></i> Sales Report
            </a>
            <a href="report_stock.php" class="btn-ghost">
                <i class="fa-solid fa-boxes-stacked"></i> Stock Report
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>Treasure successfully forged and added to the store vault!</span>
        </div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>Product details have been successfully updated.</span>
        </div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-trash-can"></i>
            <span>Product has been safely removed from the vault.</span>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>An error occurred while performing the action.</span>
        </div>
    <?php endif; ?>

    <!-- Product Table Container -->
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-family: 'Cinzel', serif; font-size: 1.25rem; color: #fff;">
                <i class="fa-solid fa-boxes-stacked" style="color: var(--haki-crimson); margin-right: 0.5rem;"></i>
                Registered Vault Inventory (<?php echo count($products); ?> Items)
            </h3>
            <span style="font-size: 0.82rem; color: var(--text-muted);">
                Logged in as Administrator: <strong style="color: var(--ocean-cyan);"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </span>
        </div>

        <div class="table-responsive">
            <table class="pirate-table">
                <thead>
                    <tr>
                        <th style="width: 60px;"><?php echo htmlspecialchars(__t('admin_col_id')); ?></th>
                        <th style="width: 70px;"><?php echo htmlspecialchars(__t('admin_col_image')); ?></th>
                        <th><?php echo htmlspecialchars(__t('admin_col_name')); ?></th>
                        <th style="width: 140px;"><?php echo htmlspecialchars(__t('admin_col_category')); ?></th>
                        <th style="width: 130px;"><?php echo htmlspecialchars(__t('admin_col_price')); ?></th>
                        <th style="width: 110px;"><?php echo htmlspecialchars(__t('admin_col_stock')); ?></th>
                        <th style="width: 160px; text-align: center;"><?php echo htmlspecialchars(__t('admin_col_actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                No products found in the database. Click "+ Add New Product" to forge your first card.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--text-muted);">#<?php echo $p['id']; ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="Card" class="table-img" onerror="this.src='images/pack/pack_op01.png'">
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #fff; font-size: 0.95rem;"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted); max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($p['description']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-cat"><?php echo htmlspecialchars($p['category']); ?></span>
                                </td>
                                <td>
                                    <strong style="color: var(--haki-crimson); font-family: 'Cinzel', serif; font-size: 1rem;">
                                        <?php echo number_format($p['price'], 2); ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php if ($p['stock'] <= 0): ?>
                                        <span class="badge-stock out-of-stock">0</span>
                                    <?php elseif ($p['stock'] < 10): ?>
                                        <span class="badge-stock low-stock"><?php echo $p['stock']; ?></span>
                                    <?php else: ?>
                                        <span class="badge-stock in-stock"><?php echo $p['stock']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-btns" style="justify-content: center;">
                                        <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn-edit btn-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> <?php echo htmlspecialchars(__t('btn_edit')); ?>
                                        </a>
                                        <a href="delete_product.php?id=<?php echo $p['id']; ?>" class="btn-delete btn-sm" onclick="return confirm('<?php echo htmlspecialchars(__t('confirm_delete')); ?>')">
                                            <i class="fa-solid fa-trash"></i> <?php echo htmlspecialchars(__t('btn_delete')); ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>

