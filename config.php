

<?php
session_start();

$host = "localhost";
$dbname = "act1";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    exit("Database connection failed. Check config.php and MySQL.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf() {
    if (
        $_SERVER['REQUEST_METHOD'] !== 'POST' ||
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit("Invalid request. Please go back and try again.");
    }
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php');
    }
}

function require_admin() {
    require_login();

    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit("Access denied. Admin only.");
    }
}

function can_manage_product($pdo, $product_id) {
    if ($_SESSION['role'] === 'admin') {
        return true;
    }

    $stmt = $pdo->prepare(
        "SELECT id FROM products WHERE id = ? AND created_by = ?"
    );
    $stmt->execute([$product_id, $_SESSION['user_id']]);

    return (bool)$stmt->fetch();
}
