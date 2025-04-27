<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check if product_id and size are provided via POST
    if (isset($_POST['product_id']) && isset($_POST['size'])) {
        $product_id = $_POST['product_id'];
        $size = $_POST['size'];

        try {
            // Fetch the size_id using the size label
            $stmt = $pdo->prepare("SELECT id FROM sizes WHERE size_label = :size LIMIT 1");
            $stmt->bindParam(':size', $size, PDO::PARAM_STR);
            $stmt->execute();
            $size_id = $stmt->fetchColumn();

            if ($size_id) {
                // Prepare and execute the query to remove the item from the wishlist
                $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
                $stmt->execute();

                // Return success response
                echo json_encode(['status' => 'success', 'message' => 'Item removed from wishlist.']);
            } else {
                // Size not found
                echo json_encode(['status' => 'error', 'message' => 'Size not found.']);
            }
        } catch (PDOException $e) {
            // Catch any database errors
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        // Missing parameters
        echo json_encode(['status' => 'error', 'message' => 'Missing product ID or size.']);
    }
} else {
    // User not logged in
    echo json_encode(['status' => 'error', 'message' => 'Please log in to remove items from your wishlist.']);
}
