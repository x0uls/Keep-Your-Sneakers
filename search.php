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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .search-results-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 0;
        }

        h2 {
            text-align: center;
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 40px;
            color: #333;
        }

        .product {
            display: inline-block;
            width: calc(33.333% - 40px);
            margin: 20px;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            text-decoration: none;
            color: inherit;
        }

        .product:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .product img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .product h3 {
            font-size: 20px;
            font-weight: 600;
            margin: 20px 0;
            color: #333;
        }

        .product p {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 20px;
            color: #333;
        }

        .no-results {
            text-align: center;
            font-size: 18px;
            font-weight: 400;
            color: #999;
        }

        .product-button {
            display: inline-block;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            background-color: #111;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .product-button:hover {
            background-color: #333;
        }

        @media (max-width: 768px) {
            .product {
                width: calc(50% - 40px);
            }
        }

        @media (max-width: 480px) {
            .product {
                width: 100%;
                margin: 10px 0;
            }

            h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <div class="search-results-container">
        <h2>Search Results</h2>

        <?php
        if ($result && count($result) > 0) {
            foreach ($result as $row) {
                echo '<a href="product_page.php?id=' . $row['id'] . '" class="product">';
                $image_path = '/products/' . $row['image']; // Assuming the image is stored in /uploads
                echo '<img src="' . $image_path . '" alt="' . $row['name'] . '">';
                echo '<h3>' . $row['name'] . '</h3>';
                echo '<p>RM ' . number_format($row['price'], 2) . '</p>';
                echo '<span class="product-button">View Product</span>';
                echo '</a>';
            }
        } else {
            echo "<p class='no-results'>No products found for '$search'</p>";
        }
        ?>

    </div>

</body>

</html>