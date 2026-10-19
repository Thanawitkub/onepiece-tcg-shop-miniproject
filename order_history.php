<?php
// order_history.php - Customer Past Purchase & Voyage Order History
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

// Authentication Check: Customer / User must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?login_required=1');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Pirate';

// Fetch customer's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC");
$stmt->execute([':uid' => $user_id]);
$orders = $stmt->fetchAll();

// Fetch order items for each order
$order_items = [];
$total_spent = 0.00;
$total_units = 0;

if (!empty($orders)) {
    $order_ids = array_column($orders, 'id');
    $placeholders = implode(',', array_fill(0, count($order_ids), '?'));
    
    $stmt_items = $pdo->prepare("
        SELECT oi.*, p.image_url, p.category 
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id IN ($placeholders)
        ORDER BY oi.id ASC
    ");
    $stmt_items->execute($order_ids);
    
    while ($item = $stmt_items->fetch()) {
        $order_items[$item['order_id']][] = $item;
        $total_units += (int)$item['quantity'];
    }

    foreach ($orders as $o) {
        $total_spent += (float)$o['total_amount'];
    }
}

// Fetch current user wallet balance
$stmt_wallet = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = :uid LIMIT 1");
$stmt_wallet->execute([':uid' => $user_id]);
$current_wallet = (float)$stmt_wallet->fetchColumn();

require_once __DIR__ . '/header.php';
?>

<main class="main-container">
    
    <!-- Page Header -->
    <div class="section-header-wrap">
        <div>
            <span class="haki-badge"><i class="fa-solid fa-scroll"></i> Log Pose Registry</span>
            <h1 class="section-title" style="margin-top: 0.5rem; display: block;">
                <?php echo htmlspecialchars(__t('order_history_title')); ?>
            </h1>
            <p class="section-subtitle">
                <?php echo htmlspecialchars(__t('order_history_subtitle')); ?>
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="index.php" class="btn-haki">
                <i class="fa-solid fa-cart-shopping"></i> <?php echo htmlspecialchars(__t('order_history_start_shopping')); ?>
            </a>
            <button type="button" onclick="window.print()" class="btn-ghost">
                <i class="fa-solid fa-print"></i> Print Records
            </button>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="metrics-row">
        <div class="metric-card cyan">
            <div class="metric-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('order_history_total_orders')); ?></h4>
                <div class="metric-num"><?php echo count($orders); ?> <span style="font-size: 0.85rem; color: var(--text-muted);">Orders</span></div>
            </div>
        </div>

        <div class="metric-card crimson">
            <div class="metric-icon">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('order_history_total_spent')); ?></h4>
                <div class="metric-num"><?php echo number_format($total_spent, 2); ?> <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars(__t('price_currency')); ?></span></div>
            </div>
        </div>

        <div class="metric-card emerald">
            <div class="metric-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('order_history_total_items')); ?></h4>
                <div class="metric-num"><?php echo number_format($total_units); ?> <span style="font-size: 0.85rem; color: var(--text-muted);">Units</span></div>
            </div>
        </div>

        <div class="metric-card gold">
            <div class="metric-icon">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('wallet_balance_label')); ?></h4>
                <div class="metric-num" style="color: #fbbf24;"><?php echo number_format($current_wallet, 2); ?> <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars(__t('price_currency')); ?></span></div>
            </div>
        </div>
    </div>

    <!-- Orders List / Empty State -->
    <?php if (empty($orders)): ?>
        <div class="admin-card" style="text-align: center; padding: 4.5rem 1.5rem;">
            <div style="width: 76px; height: 76px; margin: 0 auto 1.25rem; background: rgba(255, 23, 68, 0.1); border: 2px dashed var(--haki-crimson); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-compass-drafting" style="font-size: 2.2rem; color: var(--haki-crimson);"></i>
            </div>
            <h3 style="font-family: 'Cinzel', serif; font-size: 1.45rem; color: #fff; margin-bottom: 0.5rem;">
                <?php echo htmlspecialchars(__t('order_history_empty')); ?>
            </h3>
            <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto 1.5rem; font-size: 0.92rem;">
                You have not acquired any booster packs, display boxes, or master cartons yet. Ready to claim your first One Piece TCG treasure?
            </p>
            <a href="index.php" class="btn-haki" style="padding: 0.75rem 1.75rem;">
                <i class="fa-solid fa-anchor"></i> <?php echo htmlspecialchars(__t('order_history_start_shopping')); ?>
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php foreach ($orders as $o): ?>
                <?php 
                    $oid = $o['id'];
                    $items = $order_items[$oid] ?? [];
                ?>
                <div class="admin-card" style="margin-bottom: 0;">
                    
                    <!-- Order Card Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <span class="haki-badge" style="font-size: 0.8rem;">
                                <i class="fa-solid fa-receipt"></i> <?php echo htmlspecialchars(__t('order_history_order_no')); ?> #<?php echo $oid; ?>
                            </span>
                            <span style="font-size: 0.84rem; color: var(--text-muted);">
                                <i class="fa-solid fa-calendar-day" style="margin-right: 3px;"></i>
                                <?php echo date('Y-m-d H:i', strtotime($o['created_at'])); ?>
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span class="badge-stock in-stock">
                                <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($o['status']); ?>
                            </span>
                            <div style="text-align: right;">
                                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block;">Total Paid</span>
                                <strong style="font-size: 1.25rem; font-weight: 800; color: var(--haki-crimson); font-family: 'Cinzel', serif;">
                                    <?php echo number_format($o['total_amount'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive" style="margin: 0.75rem 0;">
                        <table class="pirate-table" style="font-size: 0.88rem;">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Card</th>
                                    <th>Product Name</th>
                                    <th style="width: 130px;">Category</th>
                                    <th style="width: 120px;">Unit Price</th>
                                    <th style="width: 90px; text-align: center;">Quantity</th>
                                    <th style="width: 130px; text-align: right;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($it['image_url'] ?? 'images/pack/pack_op01.png'); ?>" alt="Card" class="table-img" onerror="this.src='images/pack/pack_op01.png'">
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #fff; font-size: 0.92rem;"><?php echo htmlspecialchars($it['product_name']); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge-cat"><?php echo htmlspecialchars($it['category'] ?? 'One Piece'); ?></span>
                                        </td>
                                        <td>
                                            <?php echo number_format($it['unit_price'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?>
                                        </td>
                                        <td style="text-align: center; font-weight: 700; color: var(--ocean-cyan);">
                                            &times; <?php echo $it['quantity']; ?>
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: var(--haki-crimson); font-family: 'Cinzel', serif;">
                                            <?php echo number_format($it['subtotal'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Card Footer -->
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; padding-top: 0.85rem; border-top: 1px solid rgba(255, 255, 255, 0.05); font-size: 0.82rem; color: var(--text-muted);">
                        <div>
                            <i class="fa-solid fa-location-dot" style="color: var(--haki-crimson); margin-right: 4px;"></i>
                            Delivery Port: <strong style="color: #cbd5e1;"><?php echo htmlspecialchars($o['shipping_address']); ?></strong>
                            <span style="margin: 0 6px;">•</span>
                            Payment: <strong style="color: var(--ocean-cyan);"><?php echo htmlspecialchars($o['payment_method']); ?></strong>
                        </div>
                        <div>
                            Buyer: <strong style="color: #fff;"><?php echo htmlspecialchars($o['customer_name']); ?></strong>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>

