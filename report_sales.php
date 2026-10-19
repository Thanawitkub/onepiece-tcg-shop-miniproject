<?php
// report_sales.php - Sales Revenue Report (Assignment Requirement 1/2)
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

// Calculate Overall Sales Metrics
$metrics_stmt = $pdo->query("
    SELECT 
        COUNT(id) AS total_orders,
        COALESCE(SUM(total_amount), 0) AS total_revenue,
        COALESCE(AVG(total_amount), 0) AS avg_order_value
    FROM orders
");
$metrics = $metrics_stmt->fetch();

// Total items sold from order_items
$items_sold_stmt = $pdo->query("SELECT COALESCE(SUM(quantity), 0) AS total_items_sold FROM order_items");
$total_items_sold = $items_sold_stmt->fetchColumn();

// Sales breakdown by product category
$category_sales_stmt = $pdo->query("
    SELECT 
        p.category,
        COALESCE(SUM(oi.quantity), 0) AS units_sold,
        COALESCE(SUM(oi.subtotal), 0) AS category_revenue
    FROM products p
    LEFT JOIN order_items oi ON p.id = oi.product_id
    GROUP BY p.category
    ORDER BY category_revenue DESC
");
$category_sales = $category_sales_stmt->fetchAll();

// Prepare Category Chart Data
$cat_labels = [];
$cat_revenues = [];
foreach ($category_sales as $cs) {
    $cat_labels[] = $cs['category'];
    $cat_revenues[] = (float)$cs['category_revenue'];
}

// Fetch Detailed Order Transaction Log
$orders_stmt = $pdo->query("
    SELECT 
        o.id,
        o.customer_name,
        o.customer_email,
        o.payment_method,
        o.total_amount,
        o.status,
        o.created_at,
        GROUP_CONCAT(CONCAT(oi.product_name, ' (x', oi.quantity, ')') SEPARATOR '<br>') AS item_details
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.id DESC
");
$orders = $orders_stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>

<main class="main-container">
    
    <!-- Report Title Header -->
    <div class="section-header-wrap">
        <div>
            <span class="haki-badge"><i class="fa-solid fa-coins"></i> Financial Intelligence Unit</span>
            <h1 class="section-title" style="margin-top: 0.5rem; display: block;">
                <?php echo htmlspecialchars(__t('report_sales_title')); ?>
            </h1>
            <p class="section-subtitle">
                <?php echo htmlspecialchars(__t('report_sales_subtitle')); ?>
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="window.print()" class="btn-ghost">
                <i class="fa-solid fa-print"></i> Print Official Report
            </button>
            <a href="report_stock.php" class="btn-haki">
                <i class="fa-solid fa-boxes-stacked"></i> View Stock Report <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- 4 Key Metric Cards -->
    <div class="metrics-row">
        
        <div class="metric-card crimson">
            <div class="metric-icon">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('stat_total_revenue')); ?></h4>
                <div class="metric-num"><?php echo number_format($metrics['total_revenue'], 2); ?> <span style="font-size: 0.9rem; color: var(--text-muted);">THB</span></div>
            </div>
        </div>

        <div class="metric-card cyan">
            <div class="metric-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('stat_total_orders')); ?></h4>
                <div class="metric-num"><?php echo number_format($metrics['total_orders']); ?></div>
            </div>
        </div>

        <div class="metric-card gold">
            <div class="metric-icon">
                <i class="fa-solid fa-box-check"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('stat_items_sold')); ?></h4>
                <div class="metric-num"><?php echo number_format($total_items_sold); ?></div>
            </div>
        </div>

        <div class="metric-card emerald">
            <div class="metric-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="metric-info">
                <h4><?php echo htmlspecialchars(__t('stat_avg_order')); ?></h4>
                <div class="metric-num"><?php echo number_format($metrics['avg_order_value'], 2); ?> <span style="font-size: 0.9rem; color: var(--text-muted);">THB</span></div>
            </div>
        </div>

    </div>

    <!-- Graphical Breakdown & Category Revenue Table -->
    <div class="charts-row">
        
        <!-- Category Sales Revenue Chart -->
        <div class="chart-card">
            <h3><i class="fa-solid fa-chart-pie" style="color: var(--haki-crimson);"></i> Sales Revenue by Category (THB)</h3>
            <div style="height: 280px; position: relative;">
                <canvas id="categorySalesChart"></canvas>
            </div>
        </div>

        <!-- Category Summary Table -->
        <div class="chart-card">
            <h3><i class="fa-solid fa-list-check" style="color: var(--ocean-cyan);"></i> Category Share Summary</h3>
            <table class="pirate-table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Sold</th>
                        <th style="text-align: right;">Revenue (THB)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($category_sales as $cs): ?>
                        <tr>
                            <td>
                                <span class="badge-cat"><?php echo htmlspecialchars($cs['category']); ?></span>
                            </td>
                            <td><strong style="color: #fff;"><?php echo number_format($cs['units_sold']); ?></strong> units</td>
                            <td style="text-align: right; color: var(--haki-crimson); font-weight: 700;">
                                <?php echo number_format($cs['category_revenue'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Detailed Order Transaction Logs -->
    <div class="admin-card">
        <h3 style="font-family: 'Cinzel', serif; font-size: 1.25rem; color: #fff; margin-bottom: 1.25rem;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--haki-crimson); margin-right: 0.5rem;"></i>
            Recent Order Transactions Registry
        </h3>

        <div class="table-responsive">
            <table class="pirate-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Captain / Customer</th>
                        <th>Items Purchased</th>
                        <th>Payment Method</th>
                        <th>Amount (THB)</th>
                        <th>Status</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                No sales transactions recorded yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--ocean-cyan);">#<?php echo $o['id']; ?></td>
                                <td>
                                    <div style="font-weight: 700; color: #fff;"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);"><?php echo htmlspecialchars($o['customer_email']); ?></div>
                                </td>
                                <td style="font-size: 0.82rem; line-height: 1.5; color: #e2e8f0;">
                                    <?php echo $o['item_details'] ?? 'N/A'; ?>
                                </td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--text-muted);"><?php echo htmlspecialchars($o['payment_method']); ?></span>
                                </td>
                                <td>
                                    <strong style="color: var(--haki-crimson); font-family: 'Cinzel', serif; font-size: 1.05rem;">
                                        <?php echo number_format($o['total_amount'], 2); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge-stock in-stock"><?php echo htmlspecialchars($o['status']); ?></span>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-muted);">
                                    <?php echo date('Y-m-d H:i', strtotime($o['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('categorySalesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($cat_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($cat_revenues); ?>,
                    backgroundColor: [
                        '#ff1744', // Crimson Conqueror
                        '#38bdf8', // Ocean Cyan
                        '#f59e0b'  // Pirate Gold
                    ],
                    borderColor: '#050b17',
                    borderWidth: 3,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#cbd5e1',
                            font: { family: 'Plus Jakarta Sans', size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + Number(context.raw).toLocaleString() + ' THB';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>

