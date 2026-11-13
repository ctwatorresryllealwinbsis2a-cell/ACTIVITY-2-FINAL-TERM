<?php
require_once 'config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}

verify_csrf();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id == $_SESSION['user_id']) {
    redirect('users.php');
}

$stmt = $pdo->prepare(
    "UPDATE users SET status = 'pending'
     WHERE id = ? AND role = 'user'"
);
$stmt->execute([$id]);

redirect('users.php');