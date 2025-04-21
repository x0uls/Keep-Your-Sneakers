<?php
require 'db.php'; // Database connection

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Fetch sizes for the selected category
    $query_sizes = "SELECT s.size_label FROM sizes s
                    JOIN categories c ON s.category_id = c.id
                    WHERE c.id = :category_id";
    $stmt_sizes = $conn->prepare($query_sizes);
    $stmt_sizes->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt_sizes->execute();

    $sizes = $stmt_sizes->fetchAll(PDO::FETCH_ASSOC);

    if (count($sizes) > 0) {
        echo "<h3>Available Sizes:</h3><ul>";
        foreach ($sizes as $size) {
            echo "<li>" . $size['size_label'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No sizes available for this category.";
    }
}
