<?php
include '../_head.php';
include '../db.php';

try {
    $stmt = $pdo->prepare("
        SELECT p.*
        FROM products p
        JOIN product_categories pc ON p.id = pc.product_id
        JOIN categories c ON pc.category_id = c.id
        WHERE c.name = 'Men'
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}
?>

<div class="product-list-container">
    <h1>Men's Sneakers</h1>
    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>$<?php echo number_format($product['price'], 2); ?></p>
                    <a href="../product_page.php?id=<?php echo $product['id']; ?>" class="view-btn">View Product</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found in this category.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../_foot.php'; ?>