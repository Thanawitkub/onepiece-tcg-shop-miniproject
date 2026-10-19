<?php
// register.php - User Registration Form
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $error_msg = 'Please fill in all required fields to enlist.';
    } elseif ($password !== $confirm_password) {
        $error_msg = 'Passwords do not match. Please verify.';
    } elseif (strlen($password) < 6) {
        $error_msg = 'Password must be at least 6 characters long.';
    } else {
        // Check duplicate username or email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute([':u' => $username, ':e' => $email]);
        if ($stmt->fetch()) {
            $error_msg = 'That username or email is already taken by another captain.';
        } else {
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, wallet_balance, role) VALUES (:u, :e, :p, :fn, 5000.00, 'user')");
            $success = $insert_stmt->execute([
                ':u' => $username,
                ':e' => $email,
                ':p' => $hashed_password,
                ':fn' => $full_name
            ]);

            if ($success) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error_msg = 'An error occurred during registration. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<main class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-icon-wrapper" style="margin: 0 auto 1rem; width: 50px; height: 50px; font-size: 1.5rem;">
                <i class="fa-solid fa-scroll"></i>
            </div>
            <h2><?php echo htmlspecialchars(__t('register_title')); ?></h2>
            <p><?php echo htmlspecialchars(__t('register_subtitle')); ?></p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="full_name"><?php echo htmlspecialchars(__t('full_name')); ?></label>
                <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Monkey D. Luffy" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="username"><?php echo htmlspecialchars(__t('username')); ?></label>
                <input type="text" name="username" id="username" class="form-control" placeholder="pirate_king" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="email"><?php echo htmlspecialchars(__t('email')); ?></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="luffy@strawhat.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="password"><?php echo htmlspecialchars(__t('password')); ?></label>
                <input type="password" name="password" id="password" class="form-control" placeholder="•••••••• (min 6 chars)" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="confirm_password"><?php echo htmlspecialchars(__t('confirm_password')); ?></label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-haki" style="width: 100%; justify-content: center; padding: 0.85rem;">
                <i class="fa-solid fa-user-plus"></i> <?php echo htmlspecialchars(__t('register_btn')); ?>
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.75rem; font-size: 0.88rem; color: var(--text-muted);">
            <?php echo htmlspecialchars(__t('register_have_account')); ?> 
            <a href="login.php" style="color: var(--haki-crimson); font-weight: 700; text-decoration: none;">
                <?php echo htmlspecialchars(__t('register_login_link')); ?>
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>

