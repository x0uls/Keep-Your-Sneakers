<?php
session_start();
include 'db.php';

// Fetch products using PDO
try {
    $stmt = $conn->prepare("SELECT * FROM products");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
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
        <?php foreach ($result as $row) : ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <p><strong>Price:</strong> $<?= number_format($row['price'], 2) ?></p>
                <p><strong>Stock:</strong> <?= $row['stock'] > 0 ? "In Stock" : "Out of Stock" ?></p>
                <button>Add to Cart</button>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>