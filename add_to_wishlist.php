<?php
session_start();
require 'db.php'; // Make sure this sets up $pdo

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Not logged in
    echo 'User not logged in.';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    // Check if already in wishlist
    $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $exists = $stmt->fetch();

    if (!$exists) {
        // Insert into wishlist
        $insert = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        if ($insert->execute([$user_id, $product_id])) {
            echo 'Added';
        } else {
            http_response_code(500);
            echo 'Failed to add';
        }
    } else {
        echo 'Already in wishlist';
    }
} else {
    http_response_code(400); // Bad request
    echo 'Invalid request';
}
