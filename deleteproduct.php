
<?php
require_once 'config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

verify_csrf();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    redirect('products.php');
}

if (!can_manage_product($pdo, $id)) {
    http_response_code(403);
    exit("You cannot delete this product.");
}

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

redirect('products.php');
