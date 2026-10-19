<?php
// checkout.php - Process Quick Buy & Store Order Transaction
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Authentication Check: Prevent purchase if user is not logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php?error=auth_required');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_SESSION['user_id'];
    $product_id = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);
    $customer_name = trim($_POST['customer_name'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');

    // Fallbacks if empty
    if (empty($customer_name)) {
        $customer_name = $_SESSION['username'] ?? 'Pirate Captain';
    }
    if (empty($shipping_address)) {
        $shipping_address = 'Grand Line Port';
    }

    if (!$product_id || $quantity <= 0) {
        header('Location: index.php?error=invalid_order#treasure-vault');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 2. Fetch User & Wallet Balance directly from database (with row lock)
        $user_stmt = $pdo->prepare("SELECT id, username, email, full_name, wallet_balance FROM users WHERE id = :uid FOR UPDATE");
        $user_stmt->execute([':uid' => $user_id]);
        $user = $user_stmt->fetch();

        if (!$user) {
            $pdo->rollBack();
            header('Location: login.php?error=auth_required');
            exit;
        }

        $wallet_balance = (float)$user['wallet_balance'];

        // 3. Check Product stock & price (with row lock)
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id FOR UPDATE");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch();

        if (!$product || (int)$product['stock'] < $quantity) {
            $pdo->rollBack();
            header('Location: index.php?error=out_of_stock#treasure-vault');
            exit;
        }

        $unit_price = (float)$product['price'];
        $total_amount = $unit_price * $quantity;

        // 4. Balance Validation: Stop transaction if user does not have enough money
        if ($wallet_balance < $total_amount) {
            $pdo->rollBack();
            $needed = number_format($total_amount, 2);
            $current = number_format($wallet_balance, 2);
            header("Location: index.php?error=insufficient_balance&needed=" . urlencode($needed) . "&current=" . urlencode($current) . "#treasure-vault");
            exit;
        }

        // 5. Create Order
        $customer_email = !empty($user['email']) ? $user['email'] : ($user['username'] . '@strawhat.com');
        $order_stmt = $pdo->prepare("
            INSERT INTO orders (user_id, customer_name, customer_email, shipping_address, payment_method, total_amount, status)
            VALUES (:uid, :cname, :cemail, :address, 'Straw Hat Wallet', :total, 'Completed')
        ");
        $order_stmt->execute([
            ':uid' => $user_id,
            ':cname' => $customer_name,
            ':cemail' => $customer_email,
            ':address' => $shipping_address,
            ':total' => $total_amount
        ]);
        $order_id = $pdo->lastInsertId();

        // 6. Create Order Item
        $item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal)
            VALUES (:oid, :pid, :pname, :qty, :uprice, :subtotal)
        ");
        $item_stmt->execute([
            ':oid' => $order_id,
            ':pid' => $product_id,
            ':pname' => $product['name'],
            ':qty' => $quantity,
            ':uprice' => $unit_price,
            ':subtotal' => $total_amount
        ]);

        // 7. Decrement Product Stock
        $update_stock = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id");
        $update_stock->execute([
            ':qty' => $quantity,
            ':id' => $product_id
        ]);

        // 8. Deduct from User Wallet Balance
        $update_wallet = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - :total WHERE id = :uid");
        $update_wallet->execute([
            ':total' => $total_amount,
            ':uid' => $user_id
        ]);

        // Update session balance
        $_SESSION['wallet_balance'] = $wallet_balance - $total_amount;

        $pdo->commit();

        header("Location: index.php?order_success=1&order_id=$order_id#treasure-vault");
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        header('Location: index.php?error=system_error&msg=' . urlencode($e->getMessage()) . '#treasure-vault');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
