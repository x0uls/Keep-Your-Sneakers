<?php
session_start();
require 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "You must be logged in to add items to your wishlist."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['category'], $_POST['size'])) {
    $product_id = $_POST['product_id'];
    $category = $_POST['category'];
    $size_label = $_POST['size']; // Size label from the frontend
    $user_id = $_SESSION['user_id']; // Get the user_id from the session

    try {
        // Get size_id based on the size label
        $size_stmt = $pdo->prepare("SELECT id FROM sizes WHERE size_label = :size_label");
        $size_stmt->bindParam(':size_label', $size_label, PDO::PARAM_STR);
        $size_stmt->execute();
        $size_data = $size_stmt->fetch(PDO::FETCH_ASSOC);

        if ($size_data) {
            $size_id = $size_data['id'];

            // Check if product is already in the wishlist
            $wishlist_stmt = $pdo->prepare("SELECT * FROM wishlist WHERE product_id = :product_id AND size_id = :size_id AND user_id = :user_id");
            $wishlist_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $wishlist_stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
            $wishlist_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $wishlist_stmt->execute();
            $existing_wishlist_item = $wishlist_stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_wishlist_item) {
                echo json_encode(["status" => "error", "message" => "This item is already in your wishlist."]);
            } else {
                // Insert product into wishlist
                $insert_stmt = $pdo->prepare("INSERT INTO wishlist (product_id, size_id, user_id) VALUES (:product_id, :size_id, :user_id)");
                $insert_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $insert_stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
                $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $insert_stmt->execute();

                echo json_encode(["status" => "success", "message" => "Item added to wishlist."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Size not found."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
