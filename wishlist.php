<?php
session_start();
require 'db.php'; // This sets up $pdo

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch products in the user's wishlist
$stmt = $pdo->prepare("
    SELECT products.*
    FROM wishlist
    INNER JOIN products ON wishlist.product_id = products.id
    WHERE wishlist.user_id = ?
");
$stmt->execute([$user_id]);
$wishlistProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '_head.php'; ?>

<body>
    <div class="content">
        <h1>My Wishlist</h1>

        <?php if (!empty($wishlistProducts)): ?>
            <div class="wishlist-container">
                <?php foreach ($wishlistProducts as $product): ?>
                    <div class="wishlist-item">
                        <a href="product_page.php?id=<?php echo $product['id']; ?>">
                            <img src="/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p>RM <?php echo number_format($product['price'], 2); ?></p>
                        </a>
                        <a href="remove_from_wishlist.php?product_id=<?php echo $product['id']; ?>" class="remove-button">🗑 Remove</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-wishlist">
                <p>🛒 Your wishlist is empty.<br>Start adding your favorite items!</p>
                <a href="index.php" class="shop-now-button">🛍️ Shop Now</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '_foot.php'; ?>
</body>

<style>
    body {
        background-color: #f9f9f9;
    }

    .content {
        padding: 40px;
    }

    h1 {
        text-align: center;
        font-size: 36px;
        margin-bottom: 40px;
        color: #333;
    }

    .wishlist-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .wishlist-item {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        text-align: center;
        padding: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .wishlist-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .wishlist-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 15px;
    }

    .wishlist-item h3 {
        font-size: 20px;
        color: #333;
        margin: 10px 0;
    }

    .wishlist-item p {
        color: #666;
        margin-bottom: 12px;
    }

    .remove-button {
        display: inline-block;
        background-color: #ff4d4d;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .remove-button:hover {
        background-color: #e60000;
    }

    /* Empty Wishlist Styling */
    .empty-wishlist {
        text-align: center;
        margin-top: 100px;
        font-size: 24px;
        color: #999;
    }

    .empty-wishlist p {
        margin-bottom: 20px;
    }

    .shop-now-button {
        display: inline-block;
        background-color: black;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .shop-now-button:hover {
        background-color: #333;
    }
</style>

</html>