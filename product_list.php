<?php
session_start();
include 'db.php';

// Fetch products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
</head>

<body>

    <h2>Our Products</h2>

    <div class="product-container">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <p><strong>Price:</strong> $<?= number_format($row['price'], 2) ?></p>
                <p><strong>Stock:</strong> <?= $row['stock'] > 0 ? "In Stock" : "Out of Stock" ?></p>
                <button>Add to Cart</button>
            </div>
        <?php endwhile; ?>
    </div>

</body>

</html>