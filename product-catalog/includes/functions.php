<?php
/**
 * includes/functions.php
 * All reusable functions for the Product Information Catalog.
 */

const CURRENCY = '₱';      // currency symbol used by formatPrice()
const LOW_STOCK_LIMIT = 5; // stock at or below this number is "Low Stock"

/* ---------- DATA ---------- */

/** Returns the products array from data/products.php. */
function getProducts(): array
{
    return require __DIR__ . '/../data/products.php';
}

/** Returns the list of categories. */
function getCategories(): array
{
    return ['Electronics', 'Accessories', 'Home', 'Office'];
}

/** Finds one product by id, or returns null if not found. */
function findProductById(array $products, int $id): ?array
{
    foreach ($products as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

/* ---------- STRING HELPERS ---------- */

/** Shortcut: escape output to prevent XSS. */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/** Formats a number as a price, e.g. 1299 -> ₱1,299.00 */
function formatPrice(float $amount): string
{
    return CURRENCY . number_format($amount, 2);
}

/** Builds a SKU like "ELE-0001": first 3 letters of category (uppercase) + id padded with zeros. */
function generateSku(string $category, int $id): string
{
    return strtoupper(substr($category, 0, 3)) . '-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
}

/** Builds a URL-friendly slug, e.g. "Wireless Gaming Mouse" -> "wireless-gaming-mouse". */
function slugify(string $text): string
{
    $slug = strtolower(trim($text));
    return str_replace([' ', '(', ')', '.'], ['-', '', '', ''], $slug);
}

/** Shortens long text and adds "..." at the end. */
function truncateText(string $text, int $limit = 80): string
{
    if (strlen($text) <= $limit) {
        return $text;
    }
    return rtrim(substr($text, 0, $limit)) . '...';
}

/**
 * Escapes the text, then wraps the search keyword in <mark>.
 * Escaping first keeps the output safe.
 */
function highlightKeyword(string $text, string $keyword): string
{
    $safeText = e($text);
    $keyword  = trim($keyword);
    if ($keyword === '') {
        return $safeText;
    }
    $safeKeyword = preg_quote(e($keyword), '/');
    return preg_replace('/(' . $safeKeyword . ')/i', '<mark>$1</mark>', $safeText);
}

/** Converts a rating (0-5) into stars using str_repeat. */
function renderStars(int $rating): string
{
    $rating = max(0, min(5, $rating));
    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}

/** Turns the tags array into "#wireless #usb-c" text using implode. */
function formatTags(array $tags): string
{
    return '#' . implode(' #', $tags);
}

/* ---------- STOCK ---------- */

/** Returns "In Stock", "Low Stock", or "Out of Stock". */
function getStockStatus(int $stock): string
{
    if ($stock <= 0) {
        return 'Out of Stock';
    } elseif ($stock <= LOW_STOCK_LIMIT) {
        return 'Low Stock';
    }
    return 'In Stock';
}

/** Returns the CSS class for the badge color: in-stock, low-stock, or out-stock. */
function getStockClass(int $stock): string
{
    return match (getStockStatus($stock)) {
        'In Stock'  => 'in-stock',
        'Low Stock' => 'low-stock',
        default     => 'out-stock',
    };
}

/* ---------- SEARCH / FILTER / SORT ---------- */

/** Searches by name, brand, or any tag (case-insensitive). */
function searchProducts(array $products, string $keyword): array
{
    $keyword = strtolower(trim($keyword));
    if ($keyword === '') {
        return $products;
    }

    $results = [];
    foreach ($products as $product) {
        $inName  = str_contains(strtolower($product['name']), $keyword);
        $inBrand = str_contains(strtolower($product['brand']), $keyword);
        $inTags  = str_contains(strtolower(implode(' ', $product['tags'])), $keyword);

        if ($inName || $inBrand || $inTags) {
            $results[] = $product;
        }
    }
    return $results;
}

/** Keeps only products in the chosen category ("" or "all" = no filter). */
function filterByCategory(array $products, string $category): array
{
    if ($category === '' || $category === 'all') {
        return $products;
    }
    return array_values(array_filter($products, fn($p) => $p['category'] === $category));
}

/** Sorts by: name, price_asc, price_desc, or newest. */
function sortProducts(array $products, string $sortBy): array
{
    usort($products, function ($a, $b) use ($sortBy) {
        switch ($sortBy) {
            case 'price_asc':  return $a['price'] <=> $b['price'];
            case 'price_desc': return $b['price'] <=> $a['price'];
            case 'newest':     return strtotime($b['date_added']) <=> strtotime($a['date_added']);
            default:           return strcasecmp($a['name'], $b['name']); // name A-Z
        }
    });
    return $products;
}

/* ---------- SUMMARY ---------- */

/** Returns ['Electronics' => 3, 'Home' => 3, ...]. */
function countByCategory(array $products): array
{
    $counts = [];
    foreach (getCategories() as $category) {
        $counts[$category] = 0;
    }
    foreach ($products as $product) {
        $counts[$product['category']]++;
    }
    return $counts;
}

/** Total value = sum of (price x stock). */
function calculateInventoryValue(array $products): float
{
    $total = 0;
    foreach ($products as $product) {
        $total += $product['price'] * $product['stock'];
    }
    return $total;
}

/** Counts products whose status is "Low Stock". */
function countLowStock(array $products): int
{
    $count = 0;
    foreach ($products as $product) {
        if (getStockStatus($product['stock']) === 'Low Stock') {
            $count++;
        }
    }
    return $count;
}

/* ---------- RENDERING ---------- */

/** Builds the HTML of one product card. $keyword is highlighted if given. */
function renderProductCard(array $product, string $keyword = ''): string
{
    $status = getStockStatus($product['stock']);
    $class  = getStockClass($product['stock']);

    $html  = '<article class="card">';
    $html .= '  <div class="card-top">';
    $html .= '    <span class="category">' . e(ucwords($product['category'])) . '</span>';
    $html .= '    <span class="badge ' . $class . '">' . e($status) . '</span>';
    $html .= '  </div>';
    $html .= '  <h3><a href="product.php?id=' . $product['id'] . '&amp;slug=' . e(slugify($product['name'])) . '">'
           . highlightKeyword($product['name'], $keyword) . '</a></h3>';
    $html .= '  <p class="meta">' . e($product['sku']) . ' &middot; ' . highlightKeyword($product['brand'], $keyword) . '</p>';
    $html .= '  <p class="stars" title="' . $product['rating'] . ' out of 5">' . renderStars($product['rating']) . '</p>';
    $html .= '  <p class="desc">' . e(truncateText($product['description'], 80)) . '</p>';
    $html .= '  <p class="tags">' . highlightKeyword(formatTags($product['tags']), $keyword) . '</p>';
    $html .= '  <div class="card-bottom">';
    $html .= '    <strong class="price">' . e(formatPrice($product['price'])) . '</strong>';
    $html .= '    <span class="stock">Stock: ' . $product['stock'] . '</span>';
    $html .= '  </div>';
    $html .= '</article>';

    return $html;
}
