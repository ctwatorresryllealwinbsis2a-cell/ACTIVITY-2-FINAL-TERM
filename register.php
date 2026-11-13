
<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $user)) {
        $error = "Username must be 3-50 characters and contain only letters, numbers, and underscores.";
    } elseif (strlen($pass) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($pass !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE username = ?"
        );
        $stmt->execute([$user]);

        if ($stmt->fetch()) {
            $error = "Username already exists.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users
                (username, password, status, role)
                VALUES (?, ?, 'pending', 'user')"
            );

            $stmt->execute([$user, $hash]);

            $success = "Registration successful! Wait for admin approval.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | ACT1 Inventory</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">

<div class="auth-card">
    <h1>Create Account</h1>
    <p>Register for ACT1 Inventory System</p>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token"
               value="<?= e(csrf_token()) ?>">

        <label>Username</label>
        <input type="text" name="username"
               required maxlength="50">

        <label>Password</label>
        <input type="password" name="password"
               required minlength="8">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password"
               required minlength="8">

        <button class="btn primary full" type="submit">
            Register
        </button>
    </form>

    <p class="auth-link">
        Already registered?
        <a href="index.php">Login here</a>
    </p>
</div>

</body>
</html>
