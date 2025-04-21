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
    $cat_stmt->execute(['name' => $category]);
    $category_data = $cat_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category_data) {
        echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        exit;
    }

    $category_id = $category_data['id'];

    // ✅ 4. Get size ID
    $size_stmt = $pdo->prepare("SELECT id FROM sizes WHERE size_label = :label AND category_id = :category_id");
    $size_stmt->execute([
        'label' => $size_label,
        'category_id' => $category_id
    ]);
    $size_data = $size_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$size_data) {
        echo json_encode(['status' => 'error', 'message' => 'Size not found']);
        exit;
    }

    $size_id = $size_data['id'];

    // ✅ 5. Check if item already exists in cart
    $check_stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id");
    $check_stmt->execute([
        'user_id' => $user_id,
        'product_id' => $product_id,
        'size_id' => $size_id
    ]);

    if ($check_stmt->rowCount() > 0) {
        // ✅ 6. If exists, update quantity
        $update_stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id AND category_id = :category_id");
        $update_stmt->execute([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'size_id' => $size_id,
            'category_id' => $category_id
        ]);
    } else {
        // ✅ 7. If new, insert into cart
        $insert_stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, size_id, category_id, quantity) VALUES (:user_id, :product_id, :size_id, :category_id, 1)");
        $insert_stmt->execute([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'size_id' => $size_id,
            'category_id' => $category_id
        ]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
    exit;
}

// ❌ If request method isn't POST
echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
