<?php
session_start();
require 'db.php'; // Database connection
include '_head.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure ID is an integer
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Product not found.");
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
</head>

<body>

    <div class="product-container">
        <div class="product-img">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
        </div>

        <div class="product-info">
            <h2><?php echo $product['name']; ?></h2>
            <p><?php echo $product['description']; ?></p>
            <p>Category: <?php echo $product['category']; ?></p>
            <p>Price: RM<?php echo $product['price']; ?></p>
            <p>Stock: <?php echo $product['stock']; ?></p>
            <button class="add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo $product['name']; ?>" data-price="<?php echo $product['price']; ?>" data-image="<?php echo $product['image']; ?>">Add to Cart</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".add-to-cart").click(function() {
                var productId = $(this).data("id");
                var productName = $(this).data("name");
                var productPrice = $(this).data("price");
                var productImage = $(this).data("image");

                $.ajax({
                    url: "add_to_cart.php",
                    type: "POST",
                    data: {
                        id: productId,
                        name: productName,
                        price: productPrice,
                        image: productImage
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