<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];
    $product_id = $_POST['product_id'];

    $category_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :category");
    $category_stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $category_stmt->execute();
    $category_data = $category_stmt->fetch(PDO::FETCH_ASSOC);

    if ($category_data) {
        $size_stmt = $pdo->prepare("SELECT s.size_label FROM sizes s
                                    JOIN product_sizes ps ON s.id = ps.size_id
                                    WHERE s.category_id = :category_id AND ps.product_id = :product_id");
        $size_stmt->bindParam(':category_id', $category_data['id'], PDO::PARAM_INT);
        $size_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $size_stmt->execute();
        $sizes = $size_stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($sizes)) {
            foreach ($sizes as $size) {
                echo '<button>' . htmlspecialchars($size) . '</button>';
            }
        } else {
            echo '<p>No sizes available for this category.</p>';
        }
    }
    exit;
}

include '_head.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("Product not found.");
        }

        $category_stmt = $pdo->prepare("
            SELECT c.id, c.name 
            FROM categories c 
            JOIN product_categories pc ON c.id = pc.category_id
            WHERE pc.product_id = :product_id
        ");
        $category_stmt->bindParam(':product_id', $id, PDO::PARAM_INT);
        $category_stmt->execute();
        $categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

        $sizes = [];
        if ($categories) {
            $size_stmt = $pdo->prepare("
                SELECT s.size_label, ps.stock, c.name AS category_name
                FROM sizes s
                JOIN product_sizes ps ON s.id = ps.size_id
                JOIN categories c ON s.category_id = c.id
                WHERE ps.product_id = :product_id
            ");
            $size_stmt->bindParam(':product_id', $id, PDO::PARAM_INT);
            $size_stmt->execute();
            $sizes = $size_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        .product-container {
            display: flex;
            gap: 50px;
            max-width: 1200px;
            margin: auto;
        }

        .product-img {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .product-img img {
            max-width: 100%;
            border-radius: 10px;
        }

        .product-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-info h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .product-info p {
            font-size: 16px;
            margin: 6px 0;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }

        .add-to-cart {
            background-color: #111;
            color: #fff;
            border: none;
            padding: 15px 30px;
            margin-top: 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: #333;
        }

        .category-buttons {
            margin-bottom: 20px;
        }

        .category-buttons button {
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            background: #f9f9f9;
        }

        .category-buttons button.active {
            background: #111;
            color: #fff;
        }

        .size-selection {
            margin-bottom: 20px;
        }

        .size-selection button {
            padding: 10px 15px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            background: #f9f9f9;
        }

        .size-selection button:hover {
            background: #eee;
        }
    </style>
</head>

<body>

    <div class="product-container">
        <div class="product-img">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="product-info">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="price">RM<?php echo htmlspecialchars($product['price']); ?></p>
            <p><?php echo htmlspecialchars($product['description']); ?></p>

            <div>
                <h3>Available Sizes:</h3>
                <?php
                $grouped = [];
                foreach ($sizes as $size) {
                    $grouped[$size['category_name']][] = $size;
                }

                foreach ($grouped as $category_name => $category_sizes) {
                    echo "<h4>$category_name</h4><ul>";
                    foreach ($category_sizes as $s) {
                        echo "<li>{$s['size_label']} (Stock: {$s['stock']})</li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>

            <!-- Category Buttons -->
            <div class="category-buttons">
                <button class="category-button" data-category="Men">Men</button>
                <button class="category-button" data-category="Women">Women</button>
                <button class="category-button" data-category="Kids">Kids</button>
            </div>

            <div class="size-selection">
                <p><strong>Select Size:</strong></p>
                <div id="sizes">
                    <p>Select a category to view sizes.</p>
                </div>
            </div>

            <button class="add-to-cart"
                data-id="<?php echo htmlspecialchars($product['id']); ?>"
                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                data-price="<?php echo htmlspecialchars($product['price']); ?>"
                data-image="<?php echo htmlspecialchars($product['image']); ?>">
                Add to Bag
            </button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".category-button").click(function() {
                $(".category-button").removeClass("active");
                $(this).addClass("active");

                var category = $(this).data("category");

                $.ajax({
                    url: window.location.href,
                    type: "POST",
                    data: {
                        category: category,
                        product_id: <?php echo $id; ?>
                    },
                    success: function(response) {
                        $("#sizes").html(response);
                    }
                });
            });

            $(".add-to-cart").click(function() {
                var productId = $(this).data("id");

                $.ajax({
                    url: "add_to_cart.php",
                    type: "POST",
                    data: {
                        product_id: productId,
                    },
                    success: function(response) {
                        alert("Added to cart!");
                    }
                });
            });
        });
    </script>

    <?php include '_foot.php'; ?>

</body>

</html>