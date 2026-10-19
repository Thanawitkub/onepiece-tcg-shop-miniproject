<?php
// footer.php - Common Footer Component
require_once __DIR__ . '/lang.php';
?>
    <!-- Quick Buy / Order Modal -->
    <div class="modal-overlay" id="quickBuyModal">
        <div class="modal-box">
            <button class="modal-close-btn" onclick="closeQuickBuy()">&times;</button>
            <div style="text-align: center; margin-bottom: 1.25rem;">
                <span class="haki-badge"><i class="fa-solid fa-bolt"></i> <?php echo htmlspecialchars(__t('modal_checkout_badge')); ?></span>
                <h3 id="modalProductName" style="font-family: 'Cinzel', serif; margin-top: 0.5rem; color: #fff;">Product Name</h3>
            </div>
            
            <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem; background: rgba(5,12,25,0.6); padding: 1rem; border-radius: 12px;">
                <div style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <img id="modalProductImg" src="" alt="Card" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem;"><?php echo htmlspecialchars(__t('modal_price_per_unit')); ?></div>
                    <div style="font-size: 1.4rem; font-weight: 800; color: var(--haki-crimson); font-family: 'Cinzel', serif;">
                        <span id="modalProductPrice">0</span> <?php echo htmlspecialchars(__t('price_currency')); ?>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--ocean-cyan); margin-top: 2px;">
                        <?php echo htmlspecialchars(__t('modal_warehouse_stock')); ?> <span id="modalProductStock">0</span>
                    </div>
                </div>
            </div>

            <?php 
                $is_auth = isset($_SESSION['user_id']);
                $user_wallet_num = isset($wallet_balance) ? (float)$wallet_balance : 0.00;
            ?>

            <?php if (!$is_auth): ?>
                <div class="alert alert-danger" style="margin-bottom: 1.25rem; font-size: 0.85rem; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <i class="fa-solid fa-lock" style="color: #ff1744; font-size: 1.2rem;"></i>
                        <div>
                            <strong><?php echo htmlspecialchars(__t('error_auth_required_title')); ?></strong>
                            <div style="font-size: 0.78rem; color: #fecdd3; margin-top: 2px;">
                                <?php echo htmlspecialchars(__t('modal_login_required')); ?>
                            </div>
                        </div>
                    </div>
                    <a href="login.php" class="btn-haki btn-sm" style="padding: 0.4rem 0.85rem; white-space: nowrap;">
                        <i class="fa-solid fa-right-to-bracket"></i> <?php echo htmlspecialchars(__t('nav_login')); ?>
                    </a>
                </div>
            <?php else: ?>
                <!-- Current User Wallet Display inside Modal -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding: 0.65rem 0.95rem; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.4); border-radius: 10px; font-size: 0.85rem;">
                    <span style="color: #fbbf24; font-weight: 600;"><i class="fa-solid fa-coins"></i> <?php echo htmlspecialchars(__t('wallet_balance_label')); ?>:</span>
                    <span style="font-weight: 800; color: #fbbf24; font-family: 'Cinzel', serif;" id="modalUserWallet" data-balance="<?php echo $user_wallet_num; ?>">
                        💰 <?php echo number_format($user_wallet_num, 2); ?> <?php echo htmlspecialchars(__t('price_currency')); ?>
                    </span>
                </div>
            <?php endif; ?>

            <form action="checkout.php" method="POST">
                <input type="hidden" name="product_id" id="modalProductId">
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label><?php echo htmlspecialchars(__t('modal_qty_label')); ?></label>
                    <input type="number" name="quantity" id="modalQuantity" class="form-control" value="1" min="1" max="50" onchange="updateModalSubtotal()" onkeyup="updateModalSubtotal()" required>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label><?php echo htmlspecialchars(__t('modal_buyer_name')); ?></label>
                    <input type="text" name="customer_name" class="form-control" placeholder="<?php echo htmlspecialchars(__t('modal_buyer_placeholder')); ?>" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" required <?php echo !$is_auth ? 'disabled' : ''; ?>>
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label><?php echo htmlspecialchars(__t('modal_shipping_address')); ?></label>
                    <input type="text" name="shipping_address" class="form-control" placeholder="<?php echo htmlspecialchars(__t('modal_shipping_placeholder')); ?>" required <?php echo !$is_auth ? 'disabled' : ''; ?>>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.8rem 1rem; background: rgba(255,23,68,0.1); border: 1px solid rgba(255,23,68,0.3); border-radius: 10px;">
                    <span style="font-weight: 700; color: #fff;"><?php echo htmlspecialchars(__t('modal_total_bounty')); ?></span>
                    <span style="font-size: 1.5rem; font-weight: 900; color: var(--haki-crimson); font-family: 'Cinzel', serif;">
                        <span id="modalSubtotal">0</span> <?php echo htmlspecialchars(__t('price_currency')); ?>
                    </span>
                </div>

                <!-- Insufficient Balance Warning Banner (Dynamic) -->
                <div id="modalBalanceWarning" class="alert alert-danger" style="display: none; margin-bottom: 1rem; font-size: 0.82rem; padding: 0.6rem 0.9rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?php echo htmlspecialchars(__t('modal_insufficient_funds')); ?></span>
                </div>

                <?php if (!$is_auth): ?>
                    <a href="login.php" class="btn-haki" style="width: 100%; justify-content: center; padding: 0.8rem; text-decoration: none;">
                        <i class="fa-solid fa-right-to-bracket"></i> <?php echo htmlspecialchars(__t('modal_login_btn')); ?>
                    </a>
                <?php else: ?>
                    <button type="submit" id="modalSubmitBtn" class="btn-haki" style="width: 100%; justify-content: center; padding: 0.8rem;">
                        <i class="fa-solid fa-gem"></i> <?php echo htmlspecialchars(__t('modal_confirm_btn')); ?>
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-col">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div class="brand-icon-wrapper" style="width: 36px; height: 36px; font-size: 1.1rem;">
                        <i class="fa-solid fa-skull-crossbones"></i>
                    </div>
                    <h3 style="font-family: 'Cinzel', serif; font-size: 1.2rem; color: #fff;"><?php echo htmlspecialchars(__t('brand_name')); ?></h3>
                </div>
                <p><?php echo htmlspecialchars(__t('footer_about')); ?></p>
            </div>

            <div class="footer-col">
                <h4>Navigation</h4>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('nav_home')); ?></a></li>
                    <li><a href="search.php"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('nav_search')); ?></a></li>
                    <li><a href="order_history.php"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('nav_order_history')); ?></a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="report_sales.php"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('nav_report_sales')); ?></a></li>
                        <li><a href="report_stock.php"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('nav_report_stock')); ?></a></li>
                        <li><a href="admin.php"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('nav_admin')); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Bounties & Categories</h4>
                <ul class="footer-links">
                    <li><a href="index.php?category=Booster+Pack"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('cat_booster_pack')); ?></a></li>
                    <li><a href="index.php?category=Booster+Box"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('cat_booster_box')); ?></a></li>
                    <li><a href="index.php?category=Carton"><i class="fa-solid fa-angle-right"></i> <?php echo htmlspecialchars(__t('cat_carton')); ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>
                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(__t('brand_name')); ?>. <?php echo htmlspecialchars(__t('footer_rights')); ?>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span style="color: var(--haki-crimson);"><i class="fa-solid fa-shield-cat"></i> University Mini-Project Edition</span>
            </div>
        </div>
    </footer>

    <!-- Main JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>

