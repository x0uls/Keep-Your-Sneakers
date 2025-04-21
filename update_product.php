<?php
session_start();
require 'db.php';

if (!isset($_POST['product_id'])) {
    die("Product ID missing.");
}

$product_id = intval($_POST['product_id']);
$name = htmlspecialchars($_POST['name']);
$description = htmlspecialchars($_POST['description']);
$price = $_POST['price'];
$category_id = $_POST['category_id'];
$sizes = $_POST['sizes']; // Sizes and stock from the form
$new_sizes = isset($_POST['new_sizes']) ? $_POST['new_sizes'] : []; // New sizes added by the user

// Begin transaction to ensure data consistency
$pdo->beginTransaction();

// Update product details
$productStmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
$productStmt->execute([$name, $description, $price, $product_id]);

// Update or insert sizes and stock (for existing sizes)
foreach ($sizes as $sizeData) {
    $sizeId = $sizeData['size_id']; // Extract size_id
    $stock = $sizeData['stock']; // Extract stock

    if ($stock !== '' && is_numeric($stock)) {
        // Check if the size already exists in the product_sizes table
        $checkSizeStmt = $pdo->prepare("SELECT id FROM product_sizes WHERE product_id = ? AND size_id = ?");
        $checkSizeStmt->execute([$product_id, $sizeId]);
        $existingSize = $checkSizeStmt->fetchColumn();

        if ($existingSize) {
            // If the size exists, update the stock
            $updateStockStmt = $pdo->prepare("UPDATE product_sizes SET stock = ? WHERE id = ?");
            $updateStockStmt->execute([$stock, $existingSize]);
        } else {
            // If the size does not exist, insert it into the product_sizes table
            $insertSizeStmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size_id, stock) VALUES (?, ?, ?)");
            $insertSizeStmt->execute([$product_id, $sizeId, $stock]);
        }
    }
}

foreach ($new_sizes as $newSizeData) {
    $sizeId = isset($newSizeData['size_id']) ? $newSizeData['size_id'] : null;
    $stock = isset($newSizeData['stock']) ? $newSizeData['stock'] : null;

    if ($sizeId && $stock !== '' && is_numeric($stock)) {
        // Check if the size already exists in the product_sizes table
        $checkSizeStmt = $pdo->prepare("SELECT id, stock FROM product_sizes WHERE product_id = ? AND size_id = ?");
        $checkSizeStmt->execute([$product_id, $sizeId]);
        $existing = $checkSizeStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // If the size exists, ADD to the existing stock
            $newStock = $existing['stock'] + $stock;
            $updateStockStmt = $pdo->prepare("UPDATE product_sizes SET stock = ? WHERE id = ?");
            $updateStockStmt->execute([$newStock, $existing['id']]);
        } else {
            // If the size does not exist, insert it into the product_sizes table
            $insertSizeStmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size_id, stock) VALUES (?, ?, ?)");
            $insertSizeStmt->execute([$product_id, $sizeId, $stock]);
        }
    }
}


// Commit the transaction
$pdo->commit();

// Redirect or display success message
header("Location: Admin_Product_Page.php?id=" . $product_id);
exit();
