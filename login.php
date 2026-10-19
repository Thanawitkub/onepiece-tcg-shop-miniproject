<?php
// login.php - User & Admin Authentication
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

$error_msg = '';
$success_msg = '';

if (isset($_GET['registered'])) {
    $success_msg = 'Your pirate enlistment was successful! You may now login.';
}

if (isset($_GET['error']) && $_GET['error'] === 'auth_required') {
    $error_msg = __t('error_auth_required_desc');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_input = trim($_POST['username'] ?? '');
    $password_input = trim($_POST['password'] ?? '');

    if (empty($username_input) || empty($password_input)) {
        $error_msg = 'Please enter both username/email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute([':u' => $username_input, ':e' => $username_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($password_input, $user['password'])) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['wallet_balance'] = (float)($user['wallet_balance'] ?? 5000.00);

            // Redirect based on role or return URL
            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error_msg = 'Invalid pirate credentials. Please check your username and password.';
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<main class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-icon-wrapper" style="margin: 0 auto 1rem; width: 50px; height: 50px; font-size: 1.5rem;">
                <i class="fa-solid fa-skull"></i>
            </div>
            <h2><?php echo htmlspecialchars(__t('login_title')); ?></h2>
            <p><?php echo htmlspecialchars(__t('login_subtitle')); ?></p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span><?php echo htmlspecialchars($success_msg); ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label for="username"><?php echo htmlspecialchars(__t('username')); ?> or <?php echo htmlspecialchars(__t('email')); ?></label>
                <div style="position: relative;">
                    <input type="text" name="username" id="username" class="form-control" placeholder="admin or luffy" required autofocus>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password"><?php echo htmlspecialchars(__t('password')); ?></label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-haki" style="width: 100%; justify-content: center; padding: 0.85rem;">
                <i class="fa-solid fa-right-to-bracket"></i> <?php echo htmlspecialchars(__t('login_btn')); ?>
            </button>
        </form>

        <!-- Quick 1-Click Demo Logins for Evaluator/Professor -->
        <div class="demo-account-box">
            <div style="font-weight: 700; color: #fff; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-key" style="color: var(--haki-crimson);"></i> University Evaluation Demo Logins:
            </div>
            <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
                <button type="button" class="btn-ghost btn-sm" onclick="fillLogin('admin', 'admin123')">
                    <i class="fa-solid fa-shield-halved"></i> Admin (admin / admin123)
                </button>
                <button type="button" class="btn-ghost btn-sm" onclick="fillLogin('luffy', 'luffy123')">
                    <i class="fa-solid fa-coins" style="color: #fbbf24;"></i> Customer (luffy / 9,999,999 THB)
                </button>
            </div>
        </div>

        <div style="text-align: center; margin-top: 1.75rem; font-size: 0.88rem; color: var(--text-muted);">
            <?php echo htmlspecialchars(__t('login_no_account')); ?> 
            <a href="register.php" style="color: var(--haki-crimson); font-weight: 700; text-decoration: none;">
                <?php echo htmlspecialchars(__t('login_register_link')); ?>
            </a>
        </div>
    </div>
</main>

<script>
function fillLogin(user, pass) {
    document.getElementById('username').value = user;
    document.getElementById('password').value = pass;
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>

