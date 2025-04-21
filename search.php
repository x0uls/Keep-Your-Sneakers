<?php
session_start();
require 'db.php'; // Database connection
include '_head.php';

if (isset($_GET['query'])) {
    $search = trim($_GET['query']); // Clean the search input
    try {
        $sql = "SELECT * FROM products 
                WHERE name LIKE :search";

        $stmt = $pdo->prepare($sql);
        $searchTerm = "%" . $search . "%";
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $result = null;
    }
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
</head>

<body>

    <h2>Search Results</h2>

    <?php
    if ($result && count($result) > 0) {
        foreach ($result as $row) {
            echo '<a href="product_page.php?id=' . $row['id'] . '" class="product">';
            echo '<img src="' . $row['image'] . '" alt="' . $row['name'] . '">';
            echo '<h3>' . $row['name'] . '</h3>';
            echo '<p>Price: RM' . $row['price'] . '</p>';
            echo '</a>';
        }
    } else {
        echo "<p>No products found for '$search'</p>";
    }
    ?>

    <?php include '_foot.php'; ?>

</body>

</html>