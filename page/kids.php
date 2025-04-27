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
    <style>
        /* (same styles from your search page) */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            padding: 40px;
            margin-top: 100px;
        }

        .sidebar {
            width: 200px;
            min-width: 200px;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            height: fit-content;
            margin-right: 40px;
            position: sticky;
            /* This makes it stick */
            top: 20px;
            /* This sets the offset from the top of the viewport */
        }


        .search-results-container {
            flex: 1;
        }

        h2 {
            text-align: center;
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 40px;
            color: #333;
        }

        .filter-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .filter-form input[type="number"],
        .filter-form button {
            width: 180px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }

        .filter-form button {
            background-color: #111;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-form button:hover {
            background-color: #333;
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

        .price-input {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .price-input span {
            position: absolute;
            left: 10px;
            top: 40%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #666;
        }

        .price-input input {
            width: 100%;
            padding: 10px 10px 10px 30px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            text-align: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                margin-bottom: 20px;
            }

            .search-results-container {
                width: 100%;
            }

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