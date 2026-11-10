# Product Information Catalog

**Activity 9 – PHP Mini Information Management System**

A small web app built with plain PHP 8+ that displays, searches, filters, and sorts a catalog of products. All data lives in PHP arrays (no database, no frameworks, no JavaScript libraries).

## Features

- Display 12 products in a responsive card grid
- Search by product name, brand, or tag (matches are highlighted)
- Filter by category (Electronics, Accessories, Home, Office)
- Sort by name, price (low to high / high to low), or date added
- Summary section: total products, products per category, total inventory value, low-stock count
- Colored stock badges (In Stock / Low Stock / Out of Stock)
- Star ratings built with a loop-friendly string function (`str_repeat`)
- Product details page (`product.php?id=1`)
- Friendly "No products found" message
- All output escaped with `htmlspecialchars()`

## PHP Concepts Applied

| Concept | Where it is used |
|---|---|
| Arrays (array of associative arrays) | `data/products.php` |
| Nested arrays | the `tags` field of each product |
| Functions | `includes/functions.php` (`getProducts`, `searchProducts`, `sortProducts`, ...) |
| String functions | `strtoupper`, `strtolower`, `ucwords`, `trim`, `str_pad`, `substr`, `strlen`, `str_contains`, `str_replace`, `sprintf`/`number_format`, `implode`, `str_repeat` |
| Includes | `require` / `require_once` for header, footer, functions |
| GET forms | search / filter / sort form in `index.php` using `$_GET` |
| Loops | `foreach` for cards, categories, and totals |
| Conditionals | `if / elseif`, `match`, ternary for stock status, empty results, validation |

## Folder Structure

```
product-catalog/
├── index.php
├── product.php
├── data/
│   └── products.php
├── includes/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── assets/
│   └── css/style.css
├── screenshots/
│   └── .gitkeep
└── README.md
```

## How to Run Locally

**Option 1 – PHP built-in server**

```bash
cd product-catalog
php -S localhost:8000
```

Open http://localhost:8000 in your browser.

**Option 2 – XAMPP / Laragon**

1. Copy the `product-catalog` folder into `htdocs` (XAMPP) or `www` (Laragon).
2. Start Apache.
3. Open http://localhost/product-catalog/

Requires PHP 8.0 or newer.

## Screenshots

![Home](screenshots/home.png)

![Search Result](screenshots/search.png)

![Filtered and Sorted View](screenshots/filtered-sorted.png)

## Author

Name: _your name here_  
Section / Course: _your section here_
