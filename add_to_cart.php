<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// ✅ 1. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    // ✅ 2. Grab and sanitize inputs
    $product_id = intval($_POST['product_id']);
    $category = trim($_POST['category']);
    $size_label = trim($_POST['size']);

    // ✅ 3. Get category ID
    $cat_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name");
    $cat_stmt->bindParam(':name', $category, PDO::PARAM_STR);
    $cat_stmt->execute();
    $category_data = $cat_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category_data) {
        echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        exit;
    }

    $category_id = $category_data['id'];

    // ✅ 4. Get size ID
    $size_stmt = $pdo->prepare("SELECT id FROM sizes WHERE size_label = :label AND category_id = :category_id");
    $size_stmt->bindParam(':label', $size_label, PDO::PARAM_STR);
    $size_stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $size_stmt->execute();
    $size_data = $size_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$size_data) {
        echo json_encode(['status' => 'error', 'message' => 'Size not found']);
        exit;
    }

    $size_id = $size_data['id'];

    // ✅ 5. Check stock for the selected size
    $stock_stmt = $pdo->prepare("SELECT stock FROM product_sizes WHERE product_id = :product_id AND size_id = :size_id");
    $stock_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stock_stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
    $stock_stmt->execute();
    $stock_data = $stock_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stock_data || $stock_data['stock'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sorry, this size is out of stock']);
        exit;
    }

    // ✅ 6. Check if item already exists in cart
    $check_stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id");
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        // ✅ 7. If exists, update quantity
        $update_stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id");
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $update_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $update_stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
        $update_stmt->execute();
    } else {
        // ✅ 8. If new, insert into cart
        $insert_stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, size_id, category_id, quantity) VALUES (:user_id, :product_id, :size_id, :category_id, 1)");
        $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $insert_stmt->execute();
    }

    echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
    exit;
}

// ❌ If request method isn't POST
echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
