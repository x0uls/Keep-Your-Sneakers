<?php
session_start();
require 'db.php'; // Database connection
include '_head.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure ID is an integer

    try {
        // Check for product with the given ID
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("Product not found.");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?></title>
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
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
        </div>

        <div class="product-info">
            <h2><?php echo $product['name']; ?></h2>
            <p class="price">RM<?php echo $product['price']; ?></p>
            <p><?php echo $product['description']; ?></p>
            <p>Category: <?php echo $product['category']; ?></p>
            <p>Stock: <?php echo $product['stock']; ?></p>

            <div class="size-selection">
                <p><strong>Select Size:</strong></p>
                <button>UK 6</button>
                <button>UK 7</button>
                <button>UK 8</button>
                <button>UK 9</button>
                <button>UK 10</button>
                <button>UK 11</button>
                <button>UK 12</button>
            </div>

            <button class="add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo $product['name']; ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image']; ?>">Add to Bag</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
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