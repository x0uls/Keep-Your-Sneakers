<?php
session_start();
require_once 'db.php'; // adjust path if needed

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? null;

    if ($product_id) {
        try {
            // Prepare SQL query using PDO
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) 
                                   VALUES (:user_id, :product_id, 1) 
                                   ON DUPLICATE KEY UPDATE quantity = quantity + 1");

            // Bind the parameters
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

            // Execute the statement
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'DB Error']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing product_id']);
    }
}
