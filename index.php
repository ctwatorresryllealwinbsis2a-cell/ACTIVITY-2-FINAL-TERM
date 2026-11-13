
<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE username = ? LIMIT 1"
    );
    $stmt->execute([$user]);
    $account = $stmt->fetch();

    if ($account && password_verify($pass, $account['password'])) {

        if ($account['status'] !== 'approved') {
            $error = "Your account is pending approval by the administrator.";
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $account['id'];
            $_SESSION['username'] = $account['username'];
            $_SESSION['role'] = $account['role'];

            redirect('dashboard.php');
        }

    } else {
        $error = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | ACT1 Inventory</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">

<div class="auth-card">
    <h1>ACT1 Inventory</h1>
    <p>Sign in to your account</p>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token"
               value="<?= e(csrf_token()) ?>">

        <label>Username</label>
        <input type="text" name="username"
               required maxlength="50" autocomplete="username">

        <label>Password</label>
        <input type="password" name="password"
               required autocomplete="current-password">

        <button type="submit" class="btn primary full">
            Login
        </button>
    </form>

    <p class="auth-link">
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>
</div>

</body>
</html>