<?php
session_start();
require '../db.php'; // Database connection
include '../_head.php';
include '../_base.php';

// Handle price filter
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

try {
    $sql = "SELECT p.* 
    FROM products p
    JOIN product_categories pc ON p.id = pc.product_id
    JOIN categories c ON pc.category_id = c.id
    WHERE c.name = 'Kids'";

    if ($min_price !== null && $max_price !== null) {
        $sql .= " AND p.price BETWEEN :min_price AND :max_price";
    }

    $stmt = $pdo->prepare($sql);

    if ($min_price !== null && $max_price !== null) {
        $stmt->bindParam(':min_price', $min_price);
        $stmt->bindParam(':max_price', $max_price);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/pages.css">
</head>

<body>

    <div class="container">
        <!-- Sidebar for price filter -->
        <div class="sidebar">
            <h3>Filter by Price</h3>
            <div class="filter-form">
                <form method="GET" action="men.php">
                    <div class="price-input">
                        <span>RM</span>
                        <input type="number" name="min_price" placeholder="Min Price"
                            <?php if (isset($_GET['min_price'])) echo 'value="' . htmlspecialchars($_GET['min_price']) . '"'; ?> required>
                    </div>
                    <div class="price-input">
                        <span>RM</span>
                        <input type="number" name="max_price" placeholder="Max Price"
                            <?php if (isset($_GET['max_price'])) echo 'value="' . htmlspecialchars($_GET['max_price']) . '"'; ?> required>
                    </div>
                    <button type="submit">Apply Filter</button>
                </form>
            </div>
        </div>

        <!-- Product Results -->
        <div class="search-results-container">
            <h2>Kids Shoes</h2>

            <?php
            if ($result && count($result) > 0) {
                foreach ($result as $row) {
                    echo '<a href="../product_page.php?id=' . $row['id'] . '" class="product">';
                    $image_path = '../products/' . $row['image']; // Assuming the image is stored in /products
                    echo '<img src="' . $image_path . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p>RM ' . number_format($row['price'], 2) . '</p>';
                    echo '<span class="product-button">View Product</span>';
                    echo '</a>';
                }
            } else {
                echo "<p class='no-results'>No products found in 'Men' category.</p>";
            }
            ?>
        </div>
    </div>

</body>

</html>