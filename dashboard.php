
<?php
require_once 'config.php';
require_login();

$totalProducts = $pdo->query(
    "SELECT COUNT(*) FROM products"
)->fetchColumn();

$totalQuantity = $pdo->query(
    "SELECT COALESCE(SUM(quantity),0) FROM products"
)->fetchColumn();

$totalValue = $pdo->query(
    "SELECT COALESCE(SUM(quantity * price),0) FROM products"
)->fetchColumn();

$pendingUsers = 0;

if ($_SESSION['role'] === 'admin') {
    $pendingUsers = $pdo->query(
        "SELECT COUNT(*) FROM users WHERE status='pending'"
    )->fetchColumn();
}

$recent = $pdo->query(
    "SELECT p.*, u.username
     FROM products p
     LEFT JOIN users u ON p.created_by = u.id
     ORDER BY p.created_at DESC LIMIT 5"
)->fetchAll();

$page_title = "Dashboard";
include 'includes/header.php';
?>

<div class="welcome">
    <h2>Welcome to Inventory Management System</h2>
    <p>Manage your products and monitor your inventory.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Products</span>
        <h2><?= number_format($totalProducts) ?></h2>
    </div>

    <div class="stat-card">
        <span>Total Quantity</span>
        <h2><?= number_format($totalQuantity) ?></h2>
    </div>

    <div class="stat-card">
        <span>Inventory Value</span>
        <h2>₱<?= number_format($totalValue, 2) ?></h2>
    </div>

    <?php if ($_SESSION['role'] === 'admin'): ?>
    <div class="stat-card">
        <span>Pending Users</span>
        <h2><?= number_format($pendingUsers) ?></h2>
    </div>
    <?php endif; ?>
</div>

<div class="panel">
    <div class="panel-heading">
        <h3>Recent Products</h3>
        <a href="products.php" class="btn primary">View All</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recent as $p): ?>
                <tr>
                    <td><?= e($p['id']) ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= number_format($p['quantity']) ?></td>
                    <td>₱<?= number_format($p['price'], 2) ?></td>
                    <td><?= e($p['username'] ?? 'Unknown') ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$recent): ?>
                <tr><td colspan="5">No products available.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
>
