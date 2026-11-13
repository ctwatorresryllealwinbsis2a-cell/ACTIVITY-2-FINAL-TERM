
<?php
require_once 'config.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirect('products.php');
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('products.php');
}

if (!can_manage_product($pdo, $id)) {
    http_response_code(403);
    exit("You cannot edit this product.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
    $price = $_POST['price'] ?? '';

    if ($name === '' || strlen($name) > 100) {
        $error = "Invalid product name.";
    } elseif ($quantity === false || $quantity < 0) {
        $error = "Invalid quantity.";
    } elseif (!is_numeric($price) || !is_finite((float)$price)
        || (float)$price < 0 || (float)$price > 99999999.99) {
        $error = "Invalid price.";
    } else {
        $stmt = $pdo->prepare(
            "UPDATE products
             SET name = ?, description = ?, quantity = ?, price = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $name,
            $description,
            $quantity,
            number_format((float)$price, 2, '.', ''),
            $id
        ]);

        redirect('products.php');
    }
}

$page_title = "Edit Product";
include 'includes/header.php';
?>

<div class="panel form-panel">
    <h3>Edit Product</h3>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token"
               value="<?= e(csrf_token()) ?>">

        <label>Product Name</label>
        <input type="text" name="name" maxlength="100"
               value="<?= e($product['name']) ?>" required>

        <label>Description</label>
        <textarea name="description" rows="4"><?= e($product['description']) ?></textarea>

        <label>Quantity</label>
        <input type="number" name="quantity" min="0"
               value="<?= e($product['quantity']) ?>" required>

        <label>Price (₱)</label>
        <input type="number" name="price" min="0"
               max="99999999.99" step="0.01"
               value="<?= e($product['price']) ?>" required>

        <div class="form-actions">
            <button class="btn primary">Update Product</button>
            <a href="products.php" class="btn secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

