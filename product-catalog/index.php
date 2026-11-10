<?php
// index.php - main page: summary + search/filter/sort + product grid
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Home';

// 1. Read GET inputs (trim removes extra spaces)
$keyword  = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? 'all';
$sortBy   = $_GET['sort'] ?? 'name';

// 2. Validate inputs against allowed values
$categories  = getCategories();
$sortOptions = [
    'name'       => 'Name (A-Z)',
    'price_asc'  => 'Price (Low to High)',
    'price_desc' => 'Price (High to Low)',
    'newest'     => 'Date Added (Newest)',
];
if ($category !== 'all' && !in_array($category, $categories, true)) {
    $category = 'all';
}
if (!array_key_exists($sortBy, $sortOptions)) {
    $sortBy = 'name';
}

// 3. Process data: all products -> search -> filter -> sort
$allProducts = getProducts();
$results = searchProducts($allProducts, $keyword);
$results = filterByCategory($results, $category);
$results = sortProducts($results, $sortBy);

// 4. Summary values (always based on ALL products)
$totalProducts  = count($allProducts);
$categoryCounts = countByCategory($allProducts);
$inventoryValue = calculateInventoryValue($allProducts);
$lowStockCount  = countLowStock($allProducts);

require __DIR__ . '/includes/header.php';
?>

<!-- SUMMARY SECTION -->
<section class="summary">
    <div class="summary-box"><span class="label">Total Products</span><strong><?= $totalProducts ?></strong></div>
    <div class="summary-box"><span class="label">Inventory Value</span><strong><?= e(formatPrice($inventoryValue)) ?></strong></div>
    <div class="summary-box warn"><span class="label">Low-Stock Items</span><strong><?= $lowStockCount ?></strong></div>
    <div class="summary-box wide">
        <span class="label">Products per Category</span>
        <ul class="cat-list">
            <?php foreach ($categoryCounts as $name => $count): ?>
                <li><?= e($name) ?>: <strong><?= $count ?></strong></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- SEARCH / FILTER / SORT FORM (GET) -->
<form class="filters" method="get" action="index.php">
    <input type="text" name="q" placeholder="Search name, brand, or tag..." value="<?= e($keyword) ?>">

    <select name="category">
        <option value="all">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="sort">
        <?php foreach ($sortOptions as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= $sortBy === $value ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Apply</button>
    <a class="reset" href="index.php">Reset</a>
</form>

<p class="result-count">Showing <strong><?= count($results) ?></strong> of <?= $totalProducts ?> products</p>

<!-- PRODUCT GRID -->
<?php if (count($results) > 0): ?>
    <section class="grid">
        <?php foreach ($results as $product): ?>
            <?= renderProductCard($product, $keyword) ?>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <div class="empty">
        <h2>😕 No products found</h2>
        <p>Try a different keyword or category, or <a href="index.php">reset the filters</a>.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
