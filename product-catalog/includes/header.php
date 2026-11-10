<?php
// includes/header.php - shared top part of every page.
// Set $pageTitle before including this file.
$pageTitle = $pageTitle ?? 'Product Information Catalog';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Product Catalog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <h1><a href="index.php">🛒 Product Information Catalog</a></h1>
        <p>Activity 9 &ndash; PHP Mini Information Management System</p>
    </div>
</header>
<main class="container">
