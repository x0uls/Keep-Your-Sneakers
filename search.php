<?php
session_start();
require 'db.php'; // Database connection
include '_head.php';

if (isset($_GET['query'])) {
    $search = $conn->real_escape_string($_GET['query']); // Prevent SQL injection
    $sql = "SELECT * FROM products 
            WHERE name LIKE '%$search%' 
            OR description LIKE '%$search%' 
            OR category LIKE '%$search%'";

    $result = $conn->query($sql);
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
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
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