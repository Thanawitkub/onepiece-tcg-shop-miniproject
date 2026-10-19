<?php
// search.php - Search Products by Name, Category, Price Range & Sorting
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

$search_query = trim($_GET['q'] ?? '');
$search_category = trim($_GET['category'] ?? '');
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$sort_by = trim($_GET['sort'] ?? 'id_asc');

// Construct dynamic search query
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($search_query)) {
    $sql .= " AND (name LIKE :query OR description LIKE :query2)";
    $params[':query'] = "%$search_query%";
    $params[':query2'] = "%$search_query%";
}

if (!empty($search_category) && $search_category !== 'all') {
    $sql .= " AND category = :cat";
    $params[':cat'] = $search_category;
}

if ($min_price !== '') {
    $sql .= " AND price >= :min_p";
    $params[':min_p'] = $min_price;
}

if ($max_price !== '') {
    $sql .= " AND price <= :max_p";
    $params[':max_p'] = $max_price;
}

switch ($sort_by) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY name ASC";
        break;
    default:
        $sql .= " ORDER BY id ASC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>

<main class="main-container">
    
    <div style="margin-bottom: 2rem;">
        <span class="haki-badge"><i class="fa-solid fa-compass"></i> Grand Line Navigator</span>
        <h1 class="section-title" style="margin-top: 0.5rem; display: block;">
            <?php echo htmlspecialchars(__t('search_title')); ?>
        </h1>
        <p class="section-subtitle">
            Explore authentic booster packs, boxes, and cartons across all One Piece Card Game expansions.
        </p>
    </div>

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

    <!-- Search & Filter Card -->
    <div class="search-box-card">
        <form action="search.php" method="GET" class="search-form-grid">
            <div class="form-group">
                <label for="q"><i class="fa-solid fa-keyboard"></i> Search Keyword</label>
                <input type="text" name="q" id="q" class="form-control" placeholder="<?php echo htmlspecialchars(__t('search_placeholder')); ?>" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>

            <div class="form-group">
                <label for="category"><i class="fa-solid fa-tags"></i> Category</label>
                <select name="category" id="category" class="form-control">
                    <option value="all" <?php echo ($search_category === 'all' || empty($search_category)) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('search_category_all')); ?>
                    </option>
                    <option value="Booster Pack" <?php echo ($search_category === 'Booster Pack') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('cat_booster_pack')); ?>
                    </option>
                    <option value="Booster Box" <?php echo ($search_category === 'Booster Box') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('cat_booster_box')); ?>
                    </option>
                    <option value="Carton" <?php echo ($search_category === 'Carton') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('cat_carton')); ?>
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="sort"><i class="fa-solid fa-arrow-down-wide-short"></i> Sort Order</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="id_asc" <?php echo ($sort_by === 'id_asc') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('search_sort_default')); ?>
                    </option>
                    <option value="price_asc" <?php echo ($sort_by === 'price_asc') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('search_sort_price_low')); ?>
                    </option>
                    <option value="price_desc" <?php echo ($sort_by === 'price_desc') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('search_sort_price_high')); ?>
                    </option>
                    <option value="name_asc" <?php echo ($sort_by === 'name_asc') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(__t('search_sort_name')); ?>
                    </option>
                </select>
            </div>

            <button type="submit" class="btn-haki" style="height: 48px; justify-content: center;">
                <i class="fa-solid fa-magnifying-glass"></i> <?php echo htmlspecialchars(__t('search_btn')); ?>
            </button>
        </form>
    </div>

    <!-- Search Result Stats -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div style="color: var(--text-muted); font-size: 0.95rem;">
            Found <strong style="color: var(--haki-crimson); font-size: 1.15rem;"><?php echo count($results); ?></strong> <?php echo htmlspecialchars(__t('search_results_found')); ?>
            <?php if (!empty($search_query)): ?>
                for "<span style="color: #fff;"><?php echo htmlspecialchars($search_query); ?></span>"
            <?php endif; ?>
        </div>
        <?php if (!empty($search_query) || !empty($search_category) || !empty($sort_by)): ?>
            <a href="search.php" class="btn-ghost btn-sm">
                <i class="fa-solid fa-rotate-left"></i> Reset Filters
            </a>
        <?php endif; ?>
    </div>

    <!-- Results Grid -->
    <div class="products-grid">
        <?php if (empty($results)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 5rem 1rem; background: var(--bg-ocean-card); border-radius: var(--radius-lg); border: var(--glass-border);">
                <i class="fa-solid fa-compass-drafting" style="font-size: 3.5rem; color: var(--haki-crimson); margin-bottom: 1.25rem;"></i>
                <h3 style="font-family: 'Cinzel', serif; font-size: 1.4rem; color: #fff;"><?php echo htmlspecialchars(__t('search_no_results')); ?></h3>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Try refining your keywords or choosing a different category filter.</p>
                <a href="search.php" class="btn-haki" style="margin-top: 1.5rem;">
                    <i class="fa-solid fa-layer-group"></i> Browse All Cards
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($results as $item): ?>
                <?php
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

</main>

<?php require_once __DIR__ . '/footer.php'; ?>

