<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];
    $product_id = $_POST['product_id'];

    // Get category ID based on the selected category name
    $category_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :category");
    $category_stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $category_stmt->execute();
    $category_data = $category_stmt->fetch(PDO::FETCH_ASSOC);

    if ($category_data) {
        // Query for available sizes for the given product and category
        $size_stmt = $pdo->prepare("SELECT s.size_label, ps.stock FROM sizes s
                                    JOIN product_sizes ps ON s.id = ps.size_id
                                    WHERE ps.product_id = :product_id AND s.category_id = :category_id");
        $size_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $size_stmt->bindParam(':category_id', $category_data['id'], PDO::PARAM_INT);
        $size_stmt->execute();
        $sizes = $size_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($sizes)) {
            foreach ($sizes as $size) {
                echo '<button class="size-button" data-size="' . htmlspecialchars($size['size_label']) . '">'
                    . htmlspecialchars($size['size_label']) . '</button>';
            }
        } else {
            echo '<p>No sizes available for this product.</p>';
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
    <link rel="stylesheet" href="/css/product_page.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>

    <div class="product-container">
        <div class="product-img">
            <img src="/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="product-info">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="price">RM<?php echo htmlspecialchars($product['price']); ?></p>
            <!-- Render description as HTML -->
            <p><?php echo $product['description']; ?></p>

            <!-- Category Buttons -->
            <div class="category-buttons">
                <h3>Categories:</h3>
                <?php foreach ($categories as $cat): ?>
                    <button class="category-button" data-category="<?= htmlspecialchars($cat['name']) ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Size Selection -->
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

            <button class="add-to-wishlist"
                data-id="<?php echo htmlspecialchars($product['id']); ?>"
                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                data-price="<?php echo htmlspecialchars($product['price']); ?>"
                data-image="<?php echo htmlspecialchars($product['image']); ?>">
                Add to Wishlist
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
                    },
                    error: function() {
                        $("#sizes").html("<p>Unable to load sizes. Please try again.</p>");
                    }
                });
            });

            // Size selection
            $(document).on("click", ".size-button", function() {
                $(".size-button").removeClass("active");
                $(this).addClass("active");
            });

            // Add to cart
            $(".add-to-cart").click(function() {
                var productId = $(this).data("id");
                var productName = $(this).data("name");
                var productPrice = $(this).data("price");
                var productImage = $(this).data("image");

                var selectedCategory = $(".category-button.active").data("category");
                var selectedSize = $(".size-button.active").data("size");

                console.log("Product ID:", productId);
                console.log("Category:", selectedCategory);
                console.log("Size:", selectedSize);

                if (!selectedCategory || !selectedSize) {
                    alert("Please select a category and a size.");
                    return;
                }

                $.ajax({
                    url: "add_to_cart.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        product_id: productId,
                        category: selectedCategory,
                        size: selectedSize
                    },
                    success: function(response) {
                        console.log("AJAX Success Response:", response);
                        if (response.status === "success") {
                            alert(response.message);
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        alert("Something went wrong. Please try again.");
                    }
                });
            });

            // Add to wishlist
            $(".add-to-wishlist").click(function() {
                var productId = $(this).data("id");
                var productName = $(this).data("name");
                var productPrice = $(this).data("price");
                var productImage = $(this).data("image");

                var selectedCategory = $(".category-button.active").data("category");
                var selectedSize = $(".size-button.active").data("size");

                console.log("Product ID:", productId);
                console.log("Category:", selectedCategory);
                console.log("Size:", selectedSize);

                if (!selectedCategory || !selectedSize) {
                    alert("Please select a category and a size.");
                    return;
                }

                $.ajax({
                    url: "add_to_wishlist.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        product_id: productId,
                        category: selectedCategory,
                        size: selectedSize
                    },
                    success: function(response) {
                        console.log("AJAX Success Response:", response);
                        if (response.status === "success") {
                            alert(response.message);
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        alert("Something went wrong. Please try again.");
                    }
                });
            });
        });
    </script>

    <?php include '_foot.php'; ?>

</body>

</html>