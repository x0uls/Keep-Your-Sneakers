<?php
session_start();
include 'db.php'; // Ensure this file includes your PDO connection

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: LogInPage.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Get image path before deleting using PDO
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $image = $stmt->fetchColumn(); // Fetch the image column directly
    $stmt->closeCursor(); // Close cursor

    // Delete product from database using PDO
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->closeCursor();

    // Delete image file if it exists
    if (file_exists($image)) {
        unlink($image);
    }

    header("Location: admin.php");
    exit();
}
