<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);
    $stmt = $pdo->prepare("SELECT id, size_label FROM sizes WHERE category_id = ? ORDER BY size_label");
    $stmt->execute([$category_id]);
    $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($sizes);
    exit;
}

echo json_encode([]);
