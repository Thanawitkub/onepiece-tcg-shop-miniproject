<?php
// lang.php - Bilingual (EN / TH) Language Dictionary & Switcher

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle language switch via query parameter (e.g., ?lang=th or ?lang=en)
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'th'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Default language is English
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$translations = [
    'en' => [
        // Brand & Theme
        'site_title' => 'One Piece TCG Emporium | Grand Line Store',
        'brand_name' => 'GRAND LINE TCG',
        'brand_subtitle' => 'Official One Piece Card Game',
        
        // Navigation
        'nav_home' => 'Home',
        'nav_shop' => 'Browse Cards',
        'nav_search' => 'Search',
        'nav_order_history' => 'Order History',
        'nav_cart' => 'Treasure Chest',
        'nav_admin' => 'Admin Panel',
        'nav_reports' => 'Reports',
        'nav_report_sales' => 'Sales Revenue Report',
        'nav_report_stock' => 'Stock Inventory Report',
        'nav_login' => 'Login',
        'nav_register' => 'Join Crew (Register)',
        'nav_logout' => 'Log Out',
        
        // Hero Section
        'hero_badge' => '⚡ CONQUEROR\'S HAKI EXPANSION RELEASED',
        'hero_title' => 'DOMINATE THE SEAS WITH AUTHENTIC CARDS',
        'hero_subtitle' => 'Immerse into the dark rainy sea of the Grand Line. Collect sealed Booster Packs, pristine Booster Boxes, and legendary wholesale Cartons directly from Japan.',
        'hero_cta_shop' => 'Explore All Sets',
        'hero_cta_search' => 'Search Treasures',
        'hero_stat_sets' => 'Expansions (OP-01 to OP-13)',
        'hero_stat_cards' => '100% Genuine Bandai',
        'hero_stat_rating' => '5.0 Trusted Captains',

        // Hero Slider Sets
        'slide1_badge' => '⚡ 1ST ANNIVERSARY MASTERPIECE • OP-05',
        'slide1_title' => 'AWAKENING OF THE NEW ERA',
        'slide1_subtitle' => 'Featuring the legendary Gear 5 Sun God Nika Luffy and ultra-rare Oda signed card. The crown jewel of modern One Piece collecting.',
        'slide1_tag' => '1ST ANNIVERSARY',

        'slide2_badge' => '⚓ THE HISTORIC GENESIS • OP-01',
        'slide2_title' => 'ROMANCE DAWN EXPANSION',
        'slide2_subtitle' => 'The beginning of the One Piece Card Game! Hunt for legendary Manga Shanks, Leader Parallels, and original East Blue Straw Hat Pirates.',
        'slide2_tag' => 'LEGENDARY DEBUT',

        'slide3_badge' => '👑 2ND ANNIVERSARY EDITION • OP-09',
        'slide3_title' => 'THE NEW FOUR EMPERORS',
        'slide3_subtitle' => 'The clash of supreme pirate lords! Featuring Manga Shanks, Emperor Buggy, Marshall D. Teach, and Luffy with dazzling 2nd Anniv Gold specials.',
        'slide3_tag' => 'FOUR EMPERORS',

        'slide4_badge' => '🔥 LATEST PINNACLE EXPANSION • OP-13',
        'slide4_title' => 'DAWN OF THE NEW AGE',
        'slide4_subtitle' => 'The climax of high-stakes pirate battles! Factory sealed box with unprecedented secret rare pull rates and cutting-edge meta card arts.',
        'slide4_tag' => 'LATEST RELEASE',

        // Categories
        'cat_all' => 'All Treasures',
        'cat_booster_pack' => 'Booster Pack',
        'cat_booster_box' => 'Booster Box',
        'cat_carton' => 'Carton (Wholesale)',
        'filter_heading' => 'Filter By Bounty Category',

        // Product Cards
        'price_currency' => 'THB',
        'in_stock' => 'In Stock',
        'low_stock' => 'Low Stock',
        'out_of_stock' => 'Out of Stock',
        'btn_add_cart' => 'Add to Chest',
        'btn_buy_now' => 'Quick Buy',
        'btn_details' => 'View Card',
        'stock_units' => 'units left',

        // Search Page
        'search_title' => 'Grand Line Treasure Search',
        'search_placeholder' => 'Search by expansion (e.g. OP-01, Romance Dawn, Luffy)...',
        'search_category_all' => 'All Categories',
        'search_sort_default' => 'Default Sorting',
        'search_sort_price_low' => 'Price: Low to High',
        'search_sort_price_high' => 'Price: High to Low',
        'search_sort_name' => 'Name: A to Z',
        'search_btn' => 'Cast Pirate Net (Search)',
        'search_results_found' => 'Treasure(s) Found',
        'search_no_results' => 'No cards found matching your voyage search criteria.',

        // Admin & CRUD
        'admin_title' => 'Pirate Fleet - Product Management',
        'admin_add_btn' => '+ Add New Product',
        'admin_col_id' => 'ID',
        'admin_col_image' => 'Preview',
        'admin_col_name' => 'Card / Box Name',
        'admin_col_category' => 'Category',
        'admin_col_price' => 'Price (THB)',
        'admin_col_stock' => 'Stock',
        'admin_col_actions' => 'Actions',
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to discard this treasure from the vault?',

        // Add / Edit Product Forms
        'form_add_title' => 'Forge New Treasure (Add Product)',
        'form_edit_title' => 'Reforge Treasure (Edit Product)',
        'form_prod_name' => 'Product Name',
        'form_prod_cat' => 'Category',
        'form_prod_price' => 'Price (THB)',
        'form_prod_stock' => 'Stock Quantity',
        'form_prod_image' => 'Image Path / URL',
        'form_prod_desc' => 'Description & Lore',
        'form_btn_save' => 'Save Product to Vault',
        'form_btn_update' => 'Update Product Details',
        'form_btn_cancel' => 'Cancel & Return',

        // Reports
        'report_sales_title' => 'Pirate Fleet Sales & Revenue Report',
        'report_sales_subtitle' => 'Detailed accounting of revenue, transaction logs, and top-selling One Piece sets.',
        'report_stock_title' => 'Grand Line Warehouse Inventory Report',
        'report_stock_subtitle' => 'Real-time stock levels, stock warning thresholds, and total vault valuation.',
        'stat_total_revenue' => 'Total Revenue',
        'stat_total_orders' => 'Completed Orders',
        'stat_items_sold' => 'Total Units Sold',
        'stat_avg_order' => 'Avg. Order Value',
        'stat_total_stock' => 'Units in Warehouse',
        'stat_stock_value' => 'Total Inventory Valuation',
        'stat_low_stock_count' => 'Low Stock Warnings',
        'stat_out_stock_count' => 'Out of Stock Items',

        // Authentication
        'login_title' => 'Pirate Captain Login',
        'login_subtitle' => 'Enter your credentials to board the Thousand Sunny',
        'login_btn' => 'Board Ship (Login)',
        'login_no_account' => 'Don\'t have an account yet?',
        'login_register_link' => 'Join the Pirate Crew',
        'register_title' => 'Enlist In The Pirate Crew',
        'register_subtitle' => 'Create your account to purchase authentic One Piece cards',
        'register_btn' => 'Sign Pirate Pact (Register)',
        'register_have_account' => 'Already enlisted?',
        'register_login_link' => 'Login here',
        'username' => 'Username',
        'email' => 'Email Address',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'full_name' => 'Full Name / Pirate Epithet',

        // Checkout / Cart & Modal
        'cart_empty' => 'Your treasure chest is currently empty!',
        'cart_checkout' => 'Proceed to Pirate Checkout',
        'order_success' => 'Bounty Claimed! Order Placed Successfully.',
        'modal_checkout_badge' => '⚡ Straw Hat Express Checkout',
        'modal_price_per_unit' => 'Price per unit:',
        'modal_warehouse_stock' => 'Warehouse Stock:',
        'modal_qty_label' => 'Quantity to Purchase:',
        'modal_buyer_name' => 'Pirate Name / Buyer:',
        'modal_buyer_placeholder' => 'e.g. Monkey D. Luffy',
        'modal_shipping_address' => 'Grand Line Port / Shipping Address:',
        'modal_shipping_placeholder' => 'e.g. Thousand Sunny, Dock 1, Water 7',
        'modal_total_bounty' => 'Total Bounty:',
        'modal_confirm_btn' => 'Confirm Order & Purchase',
        'wallet_balance_label' => 'Wallet Balance',
        'order_success_title' => 'Bounty Claimed! Purchase Successful',
        'order_success_subtitle' => 'Your card order has been officially processed and registered into the Grand Line vault.',
        'order_receipt_id' => 'Order Registry ID:',
        'order_items_purchased' => 'Treasures Acquired:',
        'order_total_deducted' => 'Total Amount Deducted:',
        'order_wallet_remaining' => 'Remaining Wallet Balance:',
        'order_customer_name' => 'Buyer / Captain:',
        'order_shipping_dest' => 'Delivery Port:',
        'order_btn_continue' => 'Continue Treasure Hunting',
        'order_btn_view_receipt' => 'View Purchase Summary',
        'order_history_title' => 'Voyage Purchase History',
        'order_history_subtitle' => 'Track all your past booster packs, sealed boxes, and carton orders across the Grand Line.',
        'order_history_empty' => 'No past voyage purchase records found!',
        'order_history_start_shopping' => 'Embark on a Voyage & Shop Cards',
        'order_history_total_orders' => 'Total Orders Placed',
        'order_history_total_spent' => 'Total Bounty Spent',
        'order_history_total_items' => 'Total Items Collected',
        'order_history_order_no' => 'Order',
        'order_history_view_details' => 'View Receipt Details',

        // Security & Purchase Validations
        'error_auth_required_title' => 'Authentication Required',
        'error_auth_required_desc' => 'You must log in to your pirate account before purchasing treasures.',
        'error_insufficient_balance_title' => 'Insufficient Wallet Balance',
        'error_insufficient_balance_desc' => 'Your wallet balance is insufficient to complete this purchase.',
        'error_out_of_stock_title' => 'Treasure Out of Stock',
        'error_out_of_stock_desc' => 'The requested quantity exceeds available warehouse inventory.',
        'error_general_title' => 'Transaction Stopped',
        'modal_login_required' => 'Please log in to purchase this treasure.',
        'modal_login_btn' => 'Log In to Purchase',
        'modal_insufficient_funds' => '⚠️ Insufficient wallet funds for this quantity',

        // Footer
        'footer_about' => 'Grand Line TCG is the premier collector hub for official One Piece Trading Card Game collectors, captains, and enthusiasts. Featuring sealed Booster Packs, Booster Boxes, and Master Cartons.',
        'footer_rights' => 'All Rights Reserved. One Piece is copyright Eiichiro Oda / Shueisha, Toei Animation, Bandai.',
        'lang_en' => 'English',
        'lang_th' => 'ภาษาไทย'
    ],
    'th' => [
        // Brand & Theme
        'site_title' => 'One Piece TCG Emporium | ร้านการ์ดวันพีซ แกรนด์ไลน์',
        'brand_name' => 'GRAND LINE TCG',
        'brand_subtitle' => 'การ์ดเกมวันพีซแท้ 100% ลิขสิทธิ์ Bandai',
        
        // Navigation
        'nav_home' => 'หน้าแรก',
        'nav_shop' => 'เลือกซื้อการ์ด',
        'nav_search' => 'ค้นหาสินค้า',
        'nav_order_history' => 'ประวัติคำสั่งซื้อ',
        'nav_cart' => 'หีบสมบัติ (ตะกร้า)',
        'nav_admin' => 'ระบบจัดการหลังบ้าน',
        'nav_reports' => 'รายงานสรุป',
        'nav_report_sales' => 'รายงานยอดขายและรายได้',
        'nav_report_stock' => 'รายงานสินค้าคงคลัง',
        'nav_login' => 'เข้าสู่ระบบ',
        'nav_register' => 'สมัครเป็นลูกเรือ',
        'nav_logout' => 'ออกจากระบบ',
        
        // Hero Section
        'hero_badge' => '⚡ พลังฮาคิราชันย์ตื่นขึ้นแล้ว - สินค้าใหม่พร้อมจัดส่ง',
        'hero_title' => 'ครอบครองทะเลแกรนด์ไลน์ด้วยการ์ดแท้ลิขสิทธิ์',
        'hero_subtitle' => 'สัมผัสบรรยากาศทะเลรัตติกาลท่ามกลางสายฝน ดำดิ่งสู่โลก One Piece TCG ทั้งซองสุ่ม Booster Pack, กล่อง Booster Box และ ลัง Carton แท้จากญี่ปุ่น',
        'hero_cta_shop' => 'ดูการ์ดทุกชุด (OP-01 ถึง OP-13)',
        'hero_cta_search' => 'ค้นหาสมบัติ',
        'hero_stat_sets' => 'ครบทุกชุดหลัก (OP-01 ถึง OP-13)',
        'hero_stat_cards' => 'ของแท้ Bandai 100%',
        'hero_stat_rating' => '5.0 ดาว จากกัปตันทั่วไทย',

        // Hero Slider Sets
        'slide1_badge' => '⚡ มาสเตอร์พีซฉลองครบรอบ 1 ปี • OP-05',
        'slide1_title' => 'การตื่นขึ้นของยุคสมัยใหม่',
        'slide1_subtitle' => 'นำทัพโดย ลูฟี่ เกียร์ 5 ร่างเทพพระอาทิตย์นิกะ และการ์ดพร้อมลายเซ็นแท้ อ.โอดะ สุดยอดไอเทมแห่งยุคสมัย',
        'slide1_tag' => 'ฉลองครบรอบ 1 ปี',

        'slide2_badge' => '⚓ จุดเริ่มต้นแห่งตำนาน • OP-01',
        'slide2_title' => 'การผจญภัยสู่ยุคโจรสลัด ROMANCE DAWN',
        'slide2_subtitle' => 'จุดกำเนิดการ์ดเกมวันพีซระดับตำนาน! ตามล่า มังงะแชงคูส, ลีดเดอร์พาราเรล และกลุ่มโจรสลัดหมวกฟางยุคอีสต์บลู',
        'slide2_tag' => 'กล่องปฐมบท',

        'slide3_badge' => '👑 ฉลองครบรอบ 2 ปี ยิ่งใหญ่ • OP-09',
        'slide3_title' => 'สี่จักรพรรดิผู้ครองน่านน้ำ',
        'slide3_subtitle' => 'การปะทะกันของเหล่าเจ้าแห่งท้องทะเล! พบกับ มังงะแชงคูส, บากี้, ทีช หนวดดำ และลูฟี่ พร้อมสเปเชียลฟอยล์ทองฉลอง 2 ปี',
        'slide3_tag' => 'สี่จักรพรรดิ',

        'slide4_badge' => '🔥 ซีรีส์ล่าสุดขีดสุดแห่งความมันส์ • OP-13',
        'slide4_title' => 'รุ่งอรุณแห่งยุคใหม่ DAWN OF THE NEW AGE',
        'slide4_subtitle' => 'ที่สุดแห่งสงครามการ์ดบนท้องทะเล! กล่องซีลสดใหม่พร้อมเรต ซีเคร็ตแรร์ สุดอลังการ และอาร์ตเวิร์กล่าสุดของปี',
        'slide4_tag' => 'ซีรีส์ใหม่ล่าสุด',

        // Categories
        'cat_all' => 'สมบัติทั้งหมด',
        'cat_booster_pack' => 'ซองสุ่ม (Booster Pack)',
        'cat_booster_box' => 'กล่องสุ่ม (Booster Box)',
        'cat_carton' => 'ลังใหญ่ (Carton)',
        'filter_heading' => 'เลือกหมวดหมู่ตามล่าค่าหัว',

        // Product Cards
        'price_currency' => 'บาท',
        'in_stock' => 'มีสินค้าพร้อมส่ง',
        'low_stock' => 'สินค้าใกล้หมด',
        'out_of_stock' => 'สินค้าหมดชั่วคราว',
        'btn_add_cart' => 'หยิบใส่หีบ',
        'btn_buy_now' => 'สั่งซื้อด่วน',
        'btn_details' => 'ดูรายละเอียด',
        'stock_units' => 'ชิ้นสุดท้าย',

        // Search Page
        'search_title' => 'ระบบค้นหาสมบัติวันพีซ',
        'search_placeholder' => 'ค้นหาตามชื่อชุด เช่น OP-01, Romance Dawn, ลูฟี่...',
        'search_category_all' => 'ทุกหมวดหมู่',
        'search_sort_default' => 'เรียงตามค่าเริ่มต้น',
        'search_sort_price_low' => 'ราคา: ต่ำไปสูง',
        'search_sort_price_high' => 'ราคา: สูงไปต่ำ',
        'search_sort_name' => 'ชื่อ: ก - ฮ / A - Z',
        'search_btn' => 'กางตาข่ายค้นหา',
        'search_results_found' => 'รายการสมบัติที่พบ',
        'search_no_results' => 'ไม่พบสินค้าการ์ดตามเงื่อนไขที่ท่านค้นหา',

        // Admin & CRUD
        'admin_title' => 'ระบบจัดการสินค้ากองเรือโจรสลัด',
        'admin_add_btn' => '+ เพิ่มสินค้าใหม่',
        'admin_col_id' => 'รหัส',
        'admin_col_image' => 'รูปภาพ',
        'admin_col_name' => 'ชื่อการ์ด / กล่อง',
        'admin_col_category' => 'หมวดหมู่',
        'admin_col_price' => 'ราคา (บาท)',
        'admin_col_stock' => 'จำนวนคงเหลือ',
        'admin_col_actions' => 'การจัดการ',
        'btn_edit' => 'แก้ไข',
        'btn_delete' => 'ลบ',
        'confirm_delete' => 'คุณแน่ใจหรือไม่ว่าต้องการลบสมบัติชิ้นนี้ออกจากคลัง?',

        // Add / Edit Product Forms
        'form_add_title' => 'สร้างสมบัติชิ้นใหม่ (เพิ่มสินค้า)',
        'form_edit_title' => 'ปรับปรุงข้อมูลสมบัติ (แก้ไขสินค้า)',
        'form_prod_name' => 'ชื่อสินค้า',
        'form_prod_cat' => 'หมวดหมู่สินค้า',
        'form_prod_price' => 'ราคา (บาท)',
        'form_prod_stock' => 'จำนวนในสต็อก',
        'form_prod_image' => 'ที่อยู่รูปภาพ (Image Path/URL)',
        'form_prod_desc' => 'รายละเอียดและคำบรรยาย',
        'form_btn_save' => 'บันทึกเข้าสู่คลังสินค้า',
        'form_btn_update' => 'อัปเดตข้อมูลสินค้า',
        'form_btn_cancel' => 'ยกเลิกและย้อนกลับ',

        // Reports
        'report_sales_title' => 'รายงานสรุปยอดขายและรายได้กองเรือ',
        'report_sales_subtitle' => 'สรุปตัวเลขรายรับ คำสั่งซื้อ และชุดการ์ด One Piece ยอดนิยม',
        'report_stock_title' => 'รายงานตรวจสอบสินค้าคงคลังแกรนด์ไลน์',
        'report_stock_subtitle' => 'แสดงระดับสินค้าคงคลังปัจจุบัน สัญญาณเตือนสินค้าใกล้หมด และมูลค่าคลังรวม',
        'stat_total_revenue' => 'ยอดขายรวมทั้งหมด',
        'stat_total_orders' => 'จำนวนคำสั่งซื้อสำเร็จ',
        'stat_items_sold' => 'จำนวนสินค้าที่ขายได้',
        'stat_avg_order' => 'ยอดซื้อเฉลี่ยต่อออเดอร์',
        'stat_total_stock' => 'สินค้าคงเหลือในคลัง',
        'stat_stock_value' => 'มูลค่าสินค้าคงคลังรวม',
        'stat_low_stock_count' => 'สินค้าเตือนใกล้หมด',
        'stat_out_stock_count' => 'สินค้าที่หมดแล้ว',

        // Authentication
        'login_title' => 'เข้าสู่ระบบกัปตันโจรสลัด',
        'login_subtitle' => 'กรอกรหัสผ่านเพื่อขึ้นเรือเทาซันด์ ซันนี่',
        'login_btn' => 'ขึ้นเรือ (เข้าสู่ระบบ)',
        'login_no_account' => 'ยังไม่มีบัญชีลูกเรือ?',
        'login_register_link' => 'สมัครเป็นลูกเรือใหม่',
        'register_title' => 'ลงทะเบียนร่วมกลุ่มโจรสลัด',
        'register_subtitle' => 'สมัครสมาชิกร่วมผจญภัยและสะสมการ์ดวันพีซแท้',
        'register_btn' => 'ลงนามสัญญาโจรสลัด (สมัครสมาชิก)',
        'register_have_account' => 'มีบัญชีลูกเรืออยู่แล้ว?',
        'register_login_link' => 'เข้าสู่ระบบที่นี่',
        'username' => 'ชื่อผู้ใช้ (Username)',
        'email' => 'อีเมล (Email)',
        'password' => 'รหัสผ่าน (Password)',
        'confirm_password' => 'ยืนยันรหัสผ่านอีกครั้ง',
        'full_name' => 'ชื่อ-นามสกุล / ฉายาโจรสลัด',

        // Checkout / Cart & Modal
        'cart_empty' => 'หีบสมบัติของคุณยังว่างเปล่า!',
        'cart_checkout' => 'ดำเนินการชำระค่าหัว',
        'order_success' => 'เคลมค่าหัวสำเร็จ! บันทึกคำสั่งซื้อเรียบร้อยแล้ว',
        'modal_checkout_badge' => '⚡ ชำระเงินด่วนกลุ่มหมวกฟาง',
        'modal_price_per_unit' => 'ราคาต่อหน่วย:',
        'modal_warehouse_stock' => 'จำนวนในคลังสินค้า:',
        'modal_qty_label' => 'จำนวนที่ต้องการสั่งซื้อ:',
        'modal_buyer_name' => 'ชื่อผู้สั่งซื้อ / กัปตัน:',
        'modal_buyer_placeholder' => 'เช่น มังกี้ ดี. ลูฟี่',
        'modal_shipping_address' => 'ท่าเรือปลายทาง / ที่อยู่จัดส่ง:',
        'modal_shipping_placeholder' => 'เช่น เรือเทาซันด์ ซันนี่ ท่าเรือที่ 1 วอเตอร์เซเว่น',
        'modal_total_bounty' => 'ยอดรวมค่าหัว (ราคาสุทธิ):',
        'modal_confirm_btn' => 'ยืนยันการสั่งซื้อและชำระเงิน',
        'wallet_balance_label' => 'กระเป๋าเงินโจรสลัด',
        'order_success_title' => 'ล่าค่าหัวสำเร็จ! ทำรายการสั่งซื้อเรียบร้อยแล้ว',
        'order_success_subtitle' => 'คำสั่งซื้อของคุณได้รับการอนุมัติและบันทึกในทะเบียนกองเรือแกรนด์ไลน์เรียบร้อยแล้ว',
        'order_receipt_id' => 'รหัสคำสั่งซื้อ:',
        'order_items_purchased' => 'รายการสมบัติที่ได้รับ:',
        'order_total_deducted' => 'ยอดเงินที่หักชำระ:',
        'order_wallet_remaining' => 'ยอดเงินคงเหลือในกระเป๋า:',
        'order_customer_name' => 'ชื่อผู้สั่งซื้อ / กัปตัน:',
        'order_shipping_dest' => 'ท่าเรือปลายทางจัดส่ง:',
        'order_btn_continue' => 'กลับไปเลือกซื้อสมบัติต่อ',
        'order_btn_view_receipt' => 'ดูใบเสร็จสรุปคำสั่งซื้อ',
        'order_history_title' => 'ประวัติการสั่งซื้อการ์ดวันพีซ',
        'order_history_subtitle' => 'ตรวจสอบบันทึกคำสั่งซื้อซองสุ่ม กล่องยกบ็อกซ์ และยกลังทั้งหมดที่คุณเคยสั่งซื้อ',
        'order_history_empty' => 'ยังไม่มีประวัติการสั่งซื้อการ์ดในบันทึกของคุณ',
        'order_history_start_shopping' => 'ออกเดินทางเลือกซื้อการ์ด',
        'order_history_total_orders' => 'จำนวนคำสั่งซื้อทั้งหมด',
        'order_history_total_spent' => 'ยอดค่าหัวที่ใช้ไปทั้งหมด',
        'order_history_total_items' => 'จำนวนสินค้าที่สะสมได้',
        'order_history_order_no' => 'คำสั่งซื้อ',
        'order_history_view_details' => 'ดูรายละเอียดคำสั่งซื้อ',

        // Security & Purchase Validations
        'error_auth_required_title' => 'กรุณาเข้าสู่ระบบก่อนทำการสั่งซื้อ',
        'error_auth_required_desc' => 'คุณต้องเข้าสู่ระบบบัญชีโจรสลัดก่อน จึงจะสามารถซื้อสินค้าได้',
        'error_insufficient_balance_title' => 'ยอดเงินในกระเป๋าไม่เพียงพอ',
        'error_insufficient_balance_desc' => 'ยอดเงินในกระเป๋าของคุณไม่เพียงพอสำหรับการสั่งซื้อนี้',
        'error_out_of_stock_title' => 'สินค้าหมดสต็อก',
        'error_out_of_stock_desc' => 'จำนวนสินค้าที่ต้องการสั่งซื้อเกินกว่าจำนวนสินค้าคงเหลือในคลัง',
        'error_general_title' => 'การทำรายการถูกระงับ',
        'modal_login_required' => 'กรุณาเข้าสู่ระบบก่อนทำการสั่งซื้อสินค้านี้',
        'modal_login_btn' => 'เข้าสู่ระบบเพื่อสั่งซื้อ',
        'modal_insufficient_funds' => '⚠️ ยอดเงินในกระเป๋าไม่พอสำหรับจำนวนนี้',

        // Footer
        'footer_about' => 'Grand Line TCG ศูนย์รวมการ์ด One Piece Card Game ลิขสิทธิ์แท้ 100% สำหรับกัปตันและนักสะสมทั่วประเทศ ทั้งซองสุ่ม กล่องยกบ็อกซ์ และยกลังพร้อมเปิดล่าการ์ดแรร์',
        'footer_rights' => 'สงวนลิขสิทธิ์ทั้งหมด One Piece เป็นลิขสิทธิ์ของ เออิจิโระ โอดะ / ชูเอฉะ, โทเอ แอนิเมชัน, บันได',
        'lang_en' => 'English',
        'lang_th' => 'ภาษาไทย'
    ]
];

// Helper function to get translation
function __t($key) {
    global $translations, $current_lang;
    if (isset($translations[$current_lang][$key])) {
        return $translations[$current_lang][$key];
    }
    if (isset($translations['en'][$key])) {
        return $translations['en'][$key];
    }
    return $key;
}

// Function to preserve query string when switching language
function getLangSwitchUrl($targetLang) {
    $params = $_GET;
    $params['lang'] = $targetLang;
    return '?' . http_build_query($params);
}

