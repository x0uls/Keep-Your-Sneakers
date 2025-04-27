<?php
session_start();
require 'db.php'; // Make sure $pdo is loaded

if (!isset($_SESSION['user_id'])) {
    header('Location: LogInPage.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['product_id']);

// Remove the product from the wishlist
$stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);

// Redirect back to the wishlist page
header('Location: wishlist.php');
exit();
