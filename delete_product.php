<?php
// delete_product.php - Admin: Delete Product Script
require_once __DIR__ . '/config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication Check: Admin Only
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php?admin_required=1');
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: admin.php?deleted=1');
        exit;
    } catch (PDOException $e) {
        header('Location: admin.php?error=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: admin.php?error=invalid_id');
    exit;
}

