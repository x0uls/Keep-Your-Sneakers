<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

require '../db.php';

if (!isset($_POST['product_id'])) {
    die("Product ID missing.");
}

$product_id = intval($_POST['product_id']);
$name = htmlspecialchars($_POST['name']);
$description = $_POST['description'];  // Keep raw HTML for description
$price = $_POST['price'];
$category_id = $_POST['category_id'];
$sizes = $_POST['sizes'] ?? [];
$new_sizes = $_POST['new_sizes'] ?? [];

// Begin transaction
$pdo->beginTransaction();

try {
    // Fetch current product data
    $fetchStmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $fetchStmt->execute([$product_id]);
    $currentProduct = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    $currentImage = $currentProduct['image'];

    // Handle image update
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $newImageName = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $newImageTmp = $_FILES['product_image']['tmp_name'];
        $uploadPath = '../products/' . $newImageName;

        if (!move_uploaded_file($newImageTmp, $uploadPath)) {
            throw new Exception('Image upload failed.');
        }

        // Delete old image
        if ($currentImage && file_exists('../products/' . $currentImage)) {
            unlink('../products/' . $currentImage);
        }

        // Update product including the new image
        $productStmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $productStmt->execute([$name, $description, $price, $newImageName, $product_id]);
    } else {
        // No new image uploaded, update without changing image
        $productStmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
        $productStmt->execute([$name, $description, $price, $product_id]);
    }

    // Update category
    $pdo->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$product_id]);
    if (!empty($category_id)) {
        $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)")->execute([$product_id, $category_id]);
    }

    // Update sizes
    foreach ($sizes as $ps_id => $sizeData) {
        $sizeId = $sizeData['size_id'];
        $stock = $sizeData['stock'];

        if ($stock !== '' && is_numeric($stock)) {
            $updateSizeStmt = $pdo->prepare("UPDATE product_sizes SET size_id = ?, stock = ? WHERE id = ?");
            $updateSizeStmt->execute([$sizeId, $stock, $ps_id]);
        }
    }

    // Insert new sizes
    foreach ($new_sizes as $newSizeData) {
        $sizeId = $newSizeData['size_id'] ?? null;
        $stock = $newSizeData['stock'] ?? null;

        if ($sizeId && $stock !== '' && is_numeric($stock)) {
            $pdo->prepare("INSERT INTO product_sizes (product_id, size_id, stock) VALUES (?, ?, ?)")
                ->execute([$product_id, $sizeId, $stock]);
        }
    }

    // Commit transaction
    $pdo->commit();

    header("Location: edit_product.php?id=" . $product_id);
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error updating product: " . $e->getMessage());
}
