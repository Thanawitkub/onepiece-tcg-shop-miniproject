<?php
// report_stock.php - Stock Inventory Report (Assignment Requirement 2/2)
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

// Calculate Stock Metrics
$metrics_stmt = $pdo->query("
    SELECT 
        COUNT(id) AS total_products,
        COALESCE(SUM(stock), 0) AS total_stock_units,
        COALESCE(SUM(stock * price), 0) AS total_inventory_value,
        SUM(CASE WHEN stock < 10 AND stock > 0 THEN 1 ELSE 0 END) AS low_stock_count,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) AS out_of_stock_count
    FROM products
");
$metrics = $metrics_stmt->fetch();

// Stock breakdown by category
$cat_stock_stmt = $pdo->query("
    SELECT 
        category,
        COUNT(id) AS item_count,
        SUM(stock) AS total_units,
        SUM(stock * price) AS category_valuation
    FROM products
    GROUP BY category
    ORDER BY total_units DESC
");
$cat_stock = $cat_stock_stmt->fetchAll();

$cat_labels = [];
$cat_units = [];
$cat_values = [];
foreach ($cat_stock as $cs) {
    $cat_labels[] = $cs['category'];
    $cat_units[] = (int)$cs['total_units'];
    $cat_values[] = (float)$cs['category_valuation'];
}

// Fetch all products ordered by stock ascending (so low stock appears first)
$products_stmt = $pdo->query("SELECT * FROM products ORDER BY stock ASC, id ASC");
$products = $products_stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>

<main class="main-container">
    
    <!-- Report Title Header -->
    <div class="section-header-wrap">
        <div>
            <span class="haki-badge"><i class="fa-solid fa-boxes-stacked"></i> Logistics & Warehouse Department</span>
            <h1 class="section-title" style="margin-top: 0.5rem; display: block;">
                <?php echo htmlspecialchars(__t('report_stock_title')); ?>
            </h1>
            <p class="section-subtitle">
                <?php echo htmlspecialchars(__t('report_stock_subtitle')); ?>
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="window.print()" class="btn-ghost">
                <i class="fa-solid fa-print"></i> Print Official Report
            </button>
            <a href="report_sales.php" class="btn-haki">
                <i class="fa-solid fa-sack-dollar"></i> View Sales Report <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- 4 Key Stock Metric Cards -->
    <div class="metrics-row">
        
        <div class="metric-card cyan">
            <div class="metric-icon">
                <i class="fa-solid fa-cubes-stacked"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('stat_total_stock')); ?></h4>
                <div class="metric-num"><?php echo number_format($metrics['total_stock_units']); ?> <span style="font-size: 0.85rem; color: var(--text-muted);">Units</span></div>
            </div>
        </div>

        <div class="metric-card gold">
            <div class="metric-icon">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('stat_stock_value')); ?></h4>
                <div class="metric-num"><?php echo number_format($metrics['total_inventory_value'], 2); ?> <span style="font-size: 0.85rem; color: var(--text-muted);">THB</span></div>
            </div>
        </div>

        <div class="metric-card crimson">
            <div class="metric-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('stat_low_stock_count')); ?> (< 10)</h4>
                <div class="metric-num" style="color: var(--haki-crimson);"><?php echo number_format($metrics['low_stock_count']); ?></div>
            </div>
        </div>

        <div class="metric-card emerald">
            <div class="metric-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="metric-info">
                <h4>Total Product SKUs</h4>
                <div class="metric-num"><?php echo number_format($metrics['total_products']); ?></div>
            </div>
        </div>

    </div>

    <!-- Chart & Category Breakdown -->
    <div class="charts-row">
        
        <div class="chart-card">
            <h3><i class="fa-solid fa-chart-column" style="color: var(--ocean-cyan);"></i> Stock Units Distributed by Category</h3>
            <div style="height: 280px; position: relative;">
                <canvas id="categoryStockChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3><i class="fa-solid fa-table-cells" style="color: var(--haki-gold);"></i> Category Inventory Valuation</h3>
            <table class="pirate-table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Units</th>
                        <th style="text-align: right;">Valuation (THB)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cat_stock as $cs): ?>
                        <tr>
                            <td>
                                <span class="badge-cat"><?php echo htmlspecialchars($cs['category']); ?></span>
                            </td>
                            <td><strong style="color: #fff;"><?php echo number_format($cs['total_units']); ?></strong></td>
                            <td style="text-align: right; color: var(--haki-gold); font-weight: 700;">
                                <?php echo number_format($cs['category_valuation'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Full Product Stock Inventory Table -->
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-family: 'Cinzel', serif; font-size: 1.25rem; color: #fff;">
                <i class="fa-solid fa-clipboard-list" style="color: var(--ocean-cyan); margin-right: 0.5rem;"></i>
                Detailed Warehouse Stock Inventory (Sorted by Stock Urgency)
            </h3>
        </div>

        <div class="table-responsive">
            <table class="pirate-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Preview</th>
                        <th>Product / Card Name</th>
                        <th>Category</th>
                        <th>Unit Price (THB)</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th style="text-align: right;">Valuation (THB)</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <?php 
                            $stock = (int)$p['stock'];
                            $val = $stock * (float)$p['price'];
                            if ($stock <= 0) {
                                $badge = '<span class="badge-stock out-of-stock"><i class="fa-solid fa-circle-xmark"></i> Out of Stock</span>';
                            } elseif ($stock < 10) {
                                $badge = '<span class="badge-stock low-stock"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock</span>';
                            } else {
                                $badge = '<span class="badge-stock in-stock"><i class="fa-solid fa-circle-check"></i> Healthy</span>';
                            }
                        ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-muted);">#<?php echo $p['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="Card" class="table-img" onerror="this.src='images/pack/pack_op01.png'">
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #fff;"><?php echo htmlspecialchars($p['name']); ?></div>
                            </td>
                            <td>
                                <span class="badge-cat"><?php echo htmlspecialchars($p['category']); ?></span>
                            </td>
                            <td>
                                <?php echo number_format($p['price'], 2); ?>
                            </td>
                            <td>
                                <strong style="font-size: 1.05rem; color: <?php echo ($stock < 10) ? 'var(--haki-crimson)' : '#fff'; ?>;">
                                    <?php echo $stock; ?>
                                </strong>
                            </td>
                            <td>
                                <?php echo $badge; ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--haki-gold); font-family: 'Cinzel', serif;">
                                <?php echo number_format($val, 2); ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn-edit btn-sm" title="Restock or Edit">
                                    <i class="fa-solid fa-boxes-packing"></i> Restock
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('categoryStockChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($cat_labels); ?>,
                datasets: [{
                    label: 'Units in Stock',
                    data: <?php echo json_encode($cat_units); ?>,
                    backgroundColor: [
                        'rgba(56, 189, 248, 0.75)', // Ocean Cyan
                        'rgba(255, 23, 68, 0.75)',  // Crimson Conqueror
                        'rgba(245, 158, 11, 0.75)'  // Pirate Gold
                    ],
                    borderColor: [
                        '#38bdf8',
                        '#ff1744',
                        '#f59e0b'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: { color: '#cbd5e1' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    y: {
                        ticks: { color: '#cbd5e1' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>

