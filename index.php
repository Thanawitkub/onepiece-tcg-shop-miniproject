<?php
// index.php - Home Page: Hero Banner & Category-Filtered Product Showcase
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

// Retrieve Category Filter from URL
$allowed_categories = ['Booster Pack', 'Booster Box', 'Carton'];
$selected_category = isset($_GET['category']) && in_array($_GET['category'], $allowed_categories) ? $_GET['category'] : 'all';

// Build Product Query
if ($selected_category !== 'all') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = :cat ORDER BY id ASC");
    $stmt->execute([':cat' => $selected_category]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
}
$products = $stmt->fetchAll();

// Retrieve Order Success Summary if redirected from checkout
$order_summary = null;
$order_items_summary = [];
if (isset($_GET['order_success']) && isset($_GET['order_id'])) {
    $order_id = filter_var($_GET['order_id'], FILTER_VALIDATE_INT);
    if ($order_id) {
        $stmt_order = $pdo->prepare("SELECT * FROM orders WHERE id = :oid LIMIT 1");
        $stmt_order->execute([':oid' => $order_id]);
        $order_summary = $stmt_order->fetch();

        if ($order_summary) {
            $stmt_items = $pdo->prepare("
                SELECT oi.*, p.image_url, p.category 
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :oid
            ");
            $stmt_items->execute([':oid' => $order_id]);
            $order_items_summary = $stmt_items->fetchAll();
        }
    }
}

// Prepare Featured Carousel Slides
$featured_slides = [
    [
        'id' => 18,
        'name' => 'One Piece Booster Box OP-05 Awakening of the New Era',
        'badge' => __t('slide1_badge'),
        'title' => __t('slide1_title'),
        'subtitle' => __t('slide1_subtitle'),
        'tag' => __t('slide1_tag'),
        'note' => 'Featuring Manga Gear 5 Luffy & Oda Signed Card',
        'price' => 2450.00,
        'stock' => 7,
        'image_url' => 'images/box/box_op05.png'
    ],
    [
        'id' => 14,
        'name' => 'One Piece Booster Box OP-01 Romance Dawn',
        'badge' => __t('slide2_badge'),
        'title' => __t('slide2_title'),
        'subtitle' => __t('slide2_subtitle'),
        'tag' => __t('slide2_tag'),
        'note' => 'Historic 1st Edition • Manga Shanks & Parallel Zoro',
        'price' => 2350.00,
        'stock' => 24,
        'image_url' => 'images/box/box_op01.png'
    ],
    [
        'id' => 22,
        'name' => 'One Piece Booster Box OP-09 The New Four Emperors',
        'badge' => __t('slide3_badge'),
        'title' => __t('slide3_title'),
        'subtitle' => __t('slide3_subtitle'),
        'tag' => __t('slide3_tag'),
        'note' => 'Luffy, Buggy, Shanks & Teach 2nd Anniv Special',
        'price' => 2450.00,
        'stock' => 5,
        'image_url' => 'images/box/box_op09.png'
    ],
    [
        'id' => 26,
        'name' => 'One Piece Booster Box OP-13 Dawn of the New Age',
        'badge' => __t('slide4_badge'),
        'title' => __t('slide4_title'),
        'subtitle' => __t('slide4_subtitle'),
        'tag' => __t('slide4_tag'),
        'note' => 'Brand New Release • Climax Secret Rares & Alternate Arts',
        'price' => 2420.00,
        'stock' => 35,
        'image_url' => 'images/box/box_op13.png'
    ]
];

// Dynamically fetch live stock and prices from database for featured items
$featured_ids = [18, 14, 22, 26];
$stmt_feat = $pdo->query("SELECT id, name, price, stock, image_url FROM products WHERE id IN (" . implode(',', $featured_ids) . ")");
$db_products = [];
if ($stmt_feat) {
    while ($row = $stmt_feat->fetch()) {
        $db_products[$row['id']] = $row;
    }
}
foreach ($featured_slides as &$slide) {
    if (isset($db_products[$slide['id']])) {
        $slide['price'] = (float)$db_products[$slide['id']]['price'];
        $slide['stock'] = (int)$db_products[$slide['id']]['stock'];
        $slide['name'] = $db_products[$slide['id']]['name'];
        if (!empty($db_products[$slide['id']]['image_url'])) {
            $slide['image_url'] = $db_products[$slide['id']]['image_url'];
        }
    }
}
unset($slide);

// Include Header
require_once __DIR__ . '/header.php';
?>

<!-- Main Content Area -->
<main>
    
    <!-- Hero Banner Carousel (Grand Gaming Display Case with Red Neon Glow) -->
    <section class="hero-carousel-wrapper">
        <div class="hero-display-case" id="heroDisplayCase">
            
            <!-- Circular Red Navigation Arrows on Outer Edges -->
            <button type="button" class="hero-nav-arrow hero-arrow-prev" id="heroPrevBtn" aria-label="Previous Slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="hero-nav-arrow hero-arrow-next" id="heroNextBtn" aria-label="Next Slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Slider Viewport / Track -->
            <div class="hero-slider-viewport">
                <div class="hero-slider-track" id="heroSliderTrack">
                    <?php foreach ($featured_slides as $index => $slide): ?>
                        <div class="hero-slide" data-slide-index="<?php echo $index; ?>">
                            <div class="hero-slide-grid">
                                
                                <!-- Left Content -->
                                <div class="hero-content">
                                    <span class="haki-badge">
                                        <i class="fa-solid fa-cloud-bolt"></i> <?php echo htmlspecialchars($slide['badge']); ?>
                                    </span>
                                    <h1 class="hero-title">
                                        <?php 
                                            $words = explode(' ', $slide['title'], 2);
                                            if (count($words) === 2) {
                                                echo htmlspecialchars($words[0]) . ' <span class="crimson-text">' . htmlspecialchars($words[1]) . '</span>';
                                            } else {
                                                echo '<span class="crimson-text">' . htmlspecialchars($slide['title']) . '</span>';
                                            }
                                        ?>
                                    </h1>
                                    <p class="hero-subtitle">
                                        <?php echo htmlspecialchars($slide['subtitle']); ?>
                                    </p>
                                    
                                    <div class="hero-cta-group">
                                        <a href="#treasure-vault" class="btn-haki">
                                            <i class="fa-solid fa-boxes-packing"></i> <?php echo htmlspecialchars(__t('hero_cta_shop')); ?>
                                        </a>
                                        <a href="search.php" class="btn-ghost">
                                            <i class="fa-solid fa-magnifying-glass"></i> <?php echo htmlspecialchars(__t('hero_cta_search')); ?>
                                        </a>
                                    </div>

                                    <!-- Stats Bar -->
                                    <div class="hero-stats">
                                        <div class="hero-stat-item">
                                            <h3>13+</h3>
                                            <p><?php echo htmlspecialchars(__t('hero_stat_sets')); ?></p>
                                        </div>
                                        <div class="hero-stat-item">
                                            <h3>100%</h3>
                                            <p><?php echo htmlspecialchars(__t('hero_stat_cards')); ?></p>
                                        </div>
                                        <div class="hero-stat-item">
                                            <h3>5.0 ★</h3>
                                            <p><?php echo htmlspecialchars(__t('hero_stat_rating')); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right 3D Frosted Glass Showcase Card -->
                                <div class="hero-showcase">
                                    <div class="hero-glass-card">
                                        <span class="showcase-tag"><?php echo htmlspecialchars($slide['tag']); ?></span>
                                        <div class="showcase-img-wrap">
                                            <img src="<?php echo htmlspecialchars($slide['image_url']); ?>" alt="<?php echo htmlspecialchars($slide['name']); ?>">
                                        </div>
                                        <div class="showcase-details">
                                            <h3 class="showcase-title"><?php echo htmlspecialchars($slide['name']); ?></h3>
                                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                                <?php echo htmlspecialchars($slide['note']); ?>
                                            </div>
                                            <div class="showcase-price"><?php echo number_format($slide['price'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?></div>
                                            <button type="button" class="btn-haki" style="margin-top: 0.85rem; width: 100%; justify-content: center;"
                                                onclick="openQuickBuy(<?php echo (int)$slide['id']; ?>, '<?php echo addslashes($slide['name']); ?>', <?php echo (float)$slide['price']; ?>, <?php echo (int)$slide['stock']; ?>, '<?php echo addslashes($slide['image_url']); ?>')">
                                                <i class="fa-solid fa-bolt"></i> <?php echo htmlspecialchars(__t('btn_buy_now')); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pagination Dots at Bottom Center -->
            <div class="hero-pagination-dots" id="heroDots">
                <?php foreach ($featured_slides as $index => $slide): ?>
                    <button type="button" 
                            class="hero-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-index="<?php echo $index; ?>" 
                            aria-label="Go to slide <?php echo $index + 1; ?>">
                    </button>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- Product Showcase Container -->
    <section class="main-container" id="treasure-vault">
        
        <!-- Alerts / Flash Messages & Order Success Summary -->
        <?php if ($order_summary): ?>
            <div class="alert alert-success" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <i class="fa-solid fa-circle-check" style="font-size: 1.6rem; color: #34d399;"></i>
                    <div>
                        <strong style="font-size: 1.05rem;"><?php echo htmlspecialchars(__t('order_success_title')); ?></strong>
                        <div style="font-size: 0.85rem; color: #cbd5e1; margin-top: 2px;">
                            <?php echo htmlspecialchars(__t('order_receipt_id')); ?> <strong>#<?php echo htmlspecialchars($order_summary['id']); ?></strong> • <?php echo htmlspecialchars(__t('order_total_deducted')); ?> <strong style="color: var(--haki-crimson); font-family: 'Cinzel', serif;">- <?php echo number_format($order_summary['total_amount'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?></strong>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-haki btn-sm" onclick="openSuccessModal()">
                    <i class="fa-solid fa-receipt"></i> <?php echo htmlspecialchars(__t('order_btn_view_receipt')); ?>
                </button>
            </div>

            <!-- Order Success Summary Modal Screen (Compact & Centered) -->
            <div class="modal-overlay active" id="orderSuccessModal">
                <div class="modal-box" style="max-width: 500px; padding: 1.35rem 1.5rem;">
                    <button class="modal-close-btn" onclick="closeSuccessModal()" title="Close">&times;</button>
                    
                    <!-- Header -->
                    <div style="text-align: center; margin-bottom: 0.9rem;">
                        <div style="width: 46px; height: 46px; margin: 0 auto 0.5rem; background: rgba(16, 185, 129, 0.15); border: 2px solid #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 18px rgba(16, 185, 129, 0.35);">
                            <i class="fa-solid fa-check" style="font-size: 1.4rem; color: #34d399;"></i>
                        </div>
                        <span class="haki-badge" style="background: rgba(16, 185, 129, 0.2); border-color: #10b981; color: #6ee7b7; font-size: 0.72rem; padding: 0.2rem 0.7rem;">
                            <i class="fa-solid fa-stamp"></i> <?php echo htmlspecialchars(__t('order_receipt_id')); ?> #<?php echo $order_summary['id']; ?>
                        </span>
                        <h2 style="font-family: 'Cinzel', serif; font-size: 1.25rem; color: #fff; margin-top: 0.35rem; line-height: 1.2;">
                            <?php echo htmlspecialchars(__t('order_success_title')); ?>
                        </h2>
                        <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">
                            <?php echo htmlspecialchars(__t('order_success_subtitle')); ?>
                        </p>
                    </div>

                    <!-- Items Purchased List -->
                    <div style="background: rgba(5, 12, 25, 0.75); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; padding: 0.7rem 0.9rem; margin-bottom: 0.85rem; max-height: 150px; overflow-y: auto;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--ocean-cyan); text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-boxes-stacked"></i> <?php echo htmlspecialchars(__t('order_items_purchased')); ?>
                        </div>

                        <?php foreach ($order_items_summary as $it): ?>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.65rem; padding: 0.4rem 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                <div style="display: flex; align-items: center; gap: 0.65rem;">
                                    <img src="<?php echo htmlspecialchars($it['image_url'] ?? 'images/pack/pack_op01.png'); ?>" alt="Card" style="width: 36px; height: 36px; object-fit: contain; background: rgba(0,0,0,0.3); border-radius: 6px; padding: 2px;">
                                    <div>
                                        <div style="font-weight: 700; color: #fff; font-size: 0.84rem; line-height: 1.25;"><?php echo htmlspecialchars($it['product_name']); ?></div>
                                        <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 1px;">
                                            <?php echo number_format($it['unit_price'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?> &times; <strong style="color: var(--ocean-cyan);"><?php echo $it['quantity']; ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; white-space: nowrap;">
                                    <strong style="color: var(--haki-crimson); font-family: 'Cinzel', serif; font-size: 0.92rem;">
                                        <?php echo number_format($it['subtotal'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Financial & Customer Receipt Details -->
                    <div style="background: rgba(10, 22, 44, 0.6); border: 1px solid rgba(255, 23, 68, 0.25); border-radius: 10px; padding: 0.75rem 0.95rem; margin-bottom: 0.95rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem; font-size: 0.8rem;">
                            <span style="color: var(--text-muted);"><?php echo htmlspecialchars(__t('order_customer_name')); ?></span>
                            <span style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($order_summary['customer_name']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem; font-size: 0.8rem;">
                            <span style="color: var(--text-muted);"><?php echo htmlspecialchars(__t('order_shipping_dest')); ?></span>
                            <span style="color: #fff; font-weight: 500; max-width: 250px; text-align: right;"><?php echo htmlspecialchars($order_summary['shipping_address']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem; font-size: 0.8rem;">
                            <span style="color: var(--text-muted);">Payment Method:</span>
                            <span style="color: var(--ocean-cyan); font-weight: 600;"><?php echo htmlspecialchars($order_summary['payment_method']); ?></span>
                        </div>
                        <hr style="border: none; border-top: 1px dashed rgba(255, 255, 255, 0.12); margin: 0.45rem 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <span style="font-weight: 700; color: #fff; font-size: 0.85rem;"><?php echo htmlspecialchars(__t('order_total_deducted')); ?></span>
                            <span style="font-size: 1.2rem; font-weight: 900; color: var(--haki-crimson); font-family: 'Cinzel', serif;">
                                - <?php echo number_format($order_summary['total_amount'], 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?>
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem;">
                            <span style="color: #fbbf24; font-weight: 600;"><?php echo htmlspecialchars(__t('order_wallet_remaining')); ?></span>
                            <span style="font-weight: 800; color: #fbbf24; font-family: 'Cinzel', serif; font-size: 0.98rem;">
                                💰 <?php echo number_format($wallet_balance, 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div style="display: flex; gap: 0.65rem;">
                        <button type="button" class="btn-haki" style="flex: 1; justify-content: center; padding: 0.65rem;" onclick="closeSuccessModal()">
                            <i class="fa-solid fa-compass"></i> <?php echo htmlspecialchars(__t('order_btn_continue')); ?>
                        </button>
                        <button type="button" class="btn-ghost" style="padding: 0.65rem 1rem;" onclick="window.print()" title="Print Receipt">
                            <i class="fa-solid fa-print"></i>
                        </button>
                    </div>
                </div>
            </div>

            <script>
            function openSuccessModal() {
                const m = document.getElementById('orderSuccessModal');
                if (m) m.classList.add('active');
            }
            function closeSuccessModal() {
                const m = document.getElementById('orderSuccessModal');
                if (m) m.classList.remove('active');
            }
            </script>
        <?php elseif (isset($_GET['order_success'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check" style="font-size: 1.3rem;"></i>
                <div>
                    <strong><?php echo htmlspecialchars(__t('order_success')); ?></strong>
                    <div style="font-size: 0.82rem; margin-top: 2px;">Order #<?php echo htmlspecialchars($_GET['order_id'] ?? '100X'); ?> has been safely logged in the Grand Line registry.</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Error & Security Alerts -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.6rem; color: #ff1744;"></i>
                    <div>
                        <strong style="font-size: 1.05rem;">
                            <?php 
                                if ($_GET['error'] === 'insufficient_balance') {
                                    echo htmlspecialchars(__t('error_insufficient_balance_title'));
                                } elseif ($_GET['error'] === 'auth_required') {
                                    echo htmlspecialchars(__t('error_auth_required_title'));
                                } elseif ($_GET['error'] === 'out_of_stock') {
                                    echo htmlspecialchars(__t('error_out_of_stock_title'));
                                } else {
                                    echo htmlspecialchars(__t('error_general_title'));
                                }
                            ?>
                        </strong>
                        <div style="font-size: 0.85rem; color: #fecdd3; margin-top: 3px;">
                            <?php 
                                if ($_GET['error'] === 'insufficient_balance') {
                                    $needed = htmlspecialchars($_GET['needed'] ?? '');
                                    $current = htmlspecialchars($_GET['current'] ?? '');
                                    echo htmlspecialchars(__t('error_insufficient_balance_desc'));
                                    if ($needed && $current) {
                                        echo " &mdash; Total Required: <strong style='color: #fff; font-family: Cinzel, serif;'>$needed " . htmlspecialchars(__t('price_currency')) . "</strong> | Your Balance: <strong style='color: #fbbf24; font-family: Cinzel, serif;'>$current " . htmlspecialchars(__t('price_currency')) . "</strong>";
                                    }
                                } elseif ($_GET['error'] === 'auth_required') {
                                    echo htmlspecialchars(__t('error_auth_required_desc'));
                                } elseif ($_GET['error'] === 'out_of_stock') {
                                    echo htmlspecialchars(__t('error_out_of_stock_desc'));
                                } else {
                                    echo htmlspecialchars($_GET['msg'] ?? 'Transaction could not be completed.');
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <?php if ($_GET['error'] === 'auth_required'): ?>
                    <a href="login.php" class="btn-haki btn-sm">
                        <i class="fa-solid fa-right-to-bracket"></i> <?php echo htmlspecialchars(__t('nav_login')); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Category Filter & Section Title -->
        <div class="section-header-wrap">
            <div>
                <h2 class="section-title"><?php echo htmlspecialchars(__t('filter_heading')); ?></h2>
                <p class="section-subtitle">
                    <?php echo count($products); ?> treasures currently available in the Grand Line vault.
                </p>
            </div>

            <!-- Category Pills: Booster Pack / Booster Box / Carton -->
            <div class="category-filter-bar">
                <a href="index.php?category=all#treasure-vault" class="category-pill <?php echo ($selected_category === 'all') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-layer-group"></i> <?php echo htmlspecialchars(__t('cat_all')); ?>
                </a>
                <a href="index.php?category=Booster+Pack#treasure-vault" class="category-pill <?php echo ($selected_category === 'Booster Pack') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-clone"></i> <?php echo htmlspecialchars(__t('cat_booster_pack')); ?>
                </a>
                <a href="index.php?category=Booster+Box#treasure-vault" class="category-pill <?php echo ($selected_category === 'Booster Box') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box-open"></i> <?php echo htmlspecialchars(__t('cat_booster_box')); ?>
                </a>
                <a href="index.php?category=Carton#treasure-vault" class="category-pill <?php echo ($selected_category === 'Carton') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-boxes-packing"></i> <?php echo htmlspecialchars(__t('cat_carton')); ?>
                </a>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
                    <i class="fa-solid fa-anchor" style="font-size: 3rem; margin-bottom: 1rem; color: var(--haki-crimson);"></i>
                    <h3>No items found in this category.</h3>
                </div>
            <?php else: ?>
                <?php foreach ($products as $item): ?>
                    <?php
                        // Stock badge logic
                        $stock = (int)$item['stock'];
                        if ($stock <= 0) {
                            $stock_class = 'out-of-stock';
                            $stock_label = __t('out_of_stock');
                        } elseif ($stock < 15) {
                            $stock_class = 'low-stock';
                            $stock_label = __t('low_stock') . " ({$stock})";
                        } else {
                            $stock_class = 'in-stock';
                            $stock_label = __t('in_stock');
                        }
                    ?>
                    <article class="product-card">
                        <div class="card-top-badges">
                            <span class="badge-cat"><?php echo htmlspecialchars($item['category']); ?></span>
                            <span class="badge-stock <?php echo $stock_class; ?>">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 3px;"></i>
                                <?php echo htmlspecialchars($stock_label); ?>
                            </span>
                        </div>

                        <div class="card-img-container">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy">
                        </div>

                        <div class="card-body">
                            <h3 class="product-title" title="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </h3>
                            <p class="product-desc">
                                <?php echo htmlspecialchars($item['description']); ?>
                            </p>

                            <div class="card-footer">
                                <div class="price-box">
                                    <span class="price-currency"><?php echo htmlspecialchars(__t('price_currency')); ?></span>
                                    <span class="price-val"><?php echo number_format($item['price'], 2); ?></span>
                                </div>
                                <button type="button" class="card-action-btn"
                                    onclick="openQuickBuy(<?php echo $item['id']; ?>, <?php echo htmlspecialchars(json_encode($item['name'])); ?>, <?php echo $item['price']; ?>, <?php echo $item['stock']; ?>, <?php echo htmlspecialchars(json_encode($item['image_url'])); ?>)">
                                    <i class="fa-solid fa-cart-plus"></i> <?php echo htmlspecialchars(__t('btn_buy_now')); ?>
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>

