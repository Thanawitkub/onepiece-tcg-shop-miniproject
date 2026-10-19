<?php
// header.php - Common Header Component with Dark Rainy Sea Theme & Globe Language Switcher
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/lang.php';
if (!isset($pdo)) {
    require_once __DIR__ . '/config/db.php';
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$current_page = basename($_SERVER['PHP_SELF']);

// Dynamically fetch wallet balance for logged-in user
$wallet_balance = 0.00;
if ($is_logged_in && isset($pdo)) {
    $stmt_wallet = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = :uid LIMIT 1");
    $stmt_wallet->execute([':uid' => $_SESSION['user_id']]);
    $wallet_balance = (float)$stmt_wallet->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(__t('site_title')); ?></title>
    
    <!-- Google Fonts & Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Chart.js for Sales & Stock Reports -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Project Stylesheet (Dark Rainy Sea & Pirates Theme) -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Animated Rain & Conqueror's Haki Background Layers -->
    <div class="rain-container">
        <canvas id="rainCanvas" class="rain-canvas"></canvas>
        <div class="css-rain-layer"></div>
    </div>
    <div class="lightning-flash"></div>

    <!-- Sticky Glassmorphic Header Navigation -->
    <header class="site-header">
        <div class="navbar-container">
            
            <!-- Brand Logo -->
            <a href="index.php" class="brand-link">
                <div class="brand-icon-wrapper">
                    <i class="fa-solid fa-skull-crossbones"></i>
                </div>
                <div class="brand-text">
                    <h1><?php echo htmlspecialchars(__t('brand_name')); ?></h1>
                    <span><?php echo htmlspecialchars(__t('brand_subtitle')); ?></span>
                </div>
            </a>

            <!-- Navigation Links -->
            <ul class="nav-menu">
                <li>
                    <a href="index.php" class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-compass"></i>
                        <span><?php echo htmlspecialchars(__t('nav_home')); ?></span>
                    </a>
                </li>
                <li>
                    <a href="search.php" class="nav-link <?php echo ($current_page === 'search.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span><?php echo htmlspecialchars(__t('nav_search')); ?></span>
                    </a>
                </li>
                <li>
                    <a href="order_history.php" class="nav-link <?php echo ($current_page === 'order_history.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span><?php echo htmlspecialchars(__t('nav_order_history')); ?></span>
                    </a>
                </li>
                
                <!-- Admin Menus: Hidden from normal customers, visible only to admin -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <!-- Reports Dropdown Menu -->
                    <li class="nav-dropdown">
                        <a href="#" class="nav-link <?php echo (in_array($current_page, ['report_sales.php', 'report_stock.php'])) ? 'active' : ''; ?>">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span><?php echo htmlspecialchars(__t('nav_reports')); ?></span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; margin-left: 2px;"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <a href="report_sales.php" class="nav-dropdown-item">
                                <i class="fa-solid fa-sack-dollar" style="color: var(--haki-crimson);"></i>
                                <span><?php echo htmlspecialchars(__t('nav_report_sales')); ?></span>
                            </a>
                            <a href="report_stock.php" class="nav-dropdown-item">
                                <i class="fa-solid fa-boxes-stacked" style="color: var(--ocean-cyan);"></i>
                                <span><?php echo htmlspecialchars(__t('nav_report_stock')); ?></span>
                            </a>
                        </div>
                    </li>

                    <!-- Admin Management CRUD -->
                    <li>
                        <a href="admin.php" class="nav-link <?php echo (in_array($current_page, ['admin.php', 'add_product.php', 'edit_product.php'])) ? 'active' : ''; ?>">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span><?php echo htmlspecialchars(__t('nav_admin')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Top Right Corner: Language Toggle & User Auth -->
            <div class="header-actions">

                <!-- Language Switcher (Globe Icon with Dropdown) -->
                <div class="lang-switcher-wrapper">
                    <button type="button" class="lang-btn" id="langToggleBtn" title="Switch Language / เปลี่ยนภาษา">
                        <i class="fa-solid fa-globe globe-icon"></i>
                        <span><?php echo strtoupper($current_lang); ?></span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem;"></i>
                    </button>
                    <div class="lang-dropdown-menu" id="langDropdownMenu">
                        <a href="<?php echo htmlspecialchars(getLangSwitchUrl('en')); ?>" class="lang-option <?php echo ($current_lang === 'en') ? 'active' : ''; ?>">
                            <span style="font-size: 1.1rem;">🇬🇧</span>
                            <span>English (EN)</span>
                        </a>
                        <a href="<?php echo htmlspecialchars(getLangSwitchUrl('th')); ?>" class="lang-option <?php echo ($current_lang === 'th') ? 'active' : ''; ?>">
                            <span style="font-size: 1.1rem;">🇹🇭</span>
                            <span>ภาษาไทย (TH)</span>
                        </a>
                    </div>
                </div>

                <!-- User Session / Login / Register -->
                <div class="auth-buttons">
                    <?php if ($is_logged_in): ?>
                        <!-- Visual Wallet Balance Display -->
                        <div class="wallet-badge" title="<?php echo htmlspecialchars(__t('wallet_balance_label')); ?>">
                            <span class="wallet-coin">💰</span>
                            <span class="wallet-amount"><?php echo number_format($wallet_balance, 2); ?></span>
                            <span class="wallet-currency"><?php echo htmlspecialchars(__t('price_currency')); ?></span>
                        </div>

                        <div class="user-badge">
                            <i class="fa-solid fa-user-astronaut"></i>
                            <span><?php echo htmlspecialchars($username); ?></span>
                            <?php if ($user_role === 'admin'): ?>
                                <span class="admin-tag">ADMIN</span>
                            <?php endif; ?>
                        </div>
                        <a href="logout.php" class="btn-ghost" title="<?php echo htmlspecialchars(__t('nav_logout')); ?>">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn-ghost">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span><?php echo htmlspecialchars(__t('nav_login')); ?></span>
                        </a>
                        <a href="register.php" class="btn-haki">
                            <i class="fa-solid fa-user-plus"></i>
                            <span><?php echo htmlspecialchars(__t('nav_register')); ?></span>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </header>

