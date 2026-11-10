<?php
// product.php - details page for one product (product.php?id=1)
require_once __DIR__ . '/includes/functions.php';

$id      = (int) ($_GET['id'] ?? 0);
$product = findProductById(getProducts(), $id);

$pageTitle = $product ? $product['name'] : 'Product Not Found';
require __DIR__ . '/includes/header.php';
?>

<p><a class="back" href="index.php">&larr; Back to catalog</a></p>

<?php if ($product): ?>
    <article class="detail">
        <span class="category"><?= e($product['category']) ?></span>
        <h2><?= e(ucwords($product['name'])) ?></h2>
        <p class="stars"><?= renderStars($product['rating']) ?> (<?= $product['rating'] ?>/5)</p>
        <p><?= e($product['description']) ?></p>

        <table class="detail-table">
            <tr><th>SKU</th><td><?= e($product['sku']) ?></td></tr>
            <tr><th>Brand</th><td><?= e($product['brand']) ?></td></tr>
            <tr><th>Price</th><td><?= e(formatPrice($product['price'])) ?></td></tr>
            <tr>
                <th>Stock</th>
                <td>
                    <?= $product['stock'] ?> units
                    <span class="badge <?= getStockClass($product['stock']) ?>"><?= e(getStockStatus($product['stock'])) ?></span>
                </td>
            </tr>
            <tr><th>Date Added</th><td><?= e(date('F j, Y', strtotime($product['date_added']))) ?></td></tr>
            <tr><th>Tags</th><td><?= e(implode(', ', $product['tags'])) ?></td></tr>
            <tr><th>URL Slug</th><td><?= e(slugify($product['name'])) ?></td></tr>
        </table>
    </article>
<?php else: ?>
    <div class="empty">
        <h2>Product not found</h2>
        <p>That product does not exist. <a href="index.php">Return to the catalog</a>.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
