
<?php
require_once 'config.php';
require_login();

$search = trim($_GET['search'] ?? '');

$sql = "SELECT p.*, u.username
        FROM products p
        LEFT JOIN users u ON p.created_by = u.id";

$params = [];

if ($search !== '') {
    $sql .= " WHERE p.name LIKE ? OR p.description LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$page_title = "Products";
include 'includes/header.php';
?>

<div class="panel">
    <div class="panel-heading">
        <h3>Product Inventory</h3>
        <a href="addproduct.php" class="btn primary">+ Add Product</a>
    </div>

    <form method="GET" class="search-form">
        <input type="text" name="search"
               placeholder="Search product..."
               value="<?= e($search) ?>">
        <button class="btn primary">Search</button>
        <a href="products.php" class="btn secondary">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= e($p['id']) ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['description']) ?></td>
                    <td>
                        <span class="badge <?= $p['quantity'] <= 5 ? 'warning' : 'approved' ?>">
                            <?= number_format($p['quantity']) ?>
                        </span>
                    </td>
                    <td>₱<?= number_format($p['price'], 2) ?></td>
                    <td><?= e($p['username'] ?? 'Unknown') ?></td>
                    <td class="actions">
                        <?php if (can_manage_product($pdo, $p['id'])): ?>
                            <a class="btn small edit"
                               href="editproduct.php?id=<?= (int)$p['id'] ?>">
                                Edit
                            </a>

                            <form method="POST" action="deleteproduct.php"
                                  onsubmit="return confirm('Delete this product?')">
                                <input type="hidden" name="csrf_token"
                                       value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id"
                                       value="<?= (int)$p['id'] ?>">
                                <button class="btn small danger">
                                    Delete
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="muted">View only</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$products): ?>
                <tr>
                    <td colspan="7">No products found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

