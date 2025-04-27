<?php
session_start();
require 'db.php'; // Database connection
include '_head.php'; // Include head only once

// Fetch top 5 best-selling products
try {
    $sql = "SELECT * FROM products ORDER BY sold_count DESC LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bestsellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $bestsellers = [];
}
?>

<body>

    <div class="content">

        <!-- Ad Space Section -->
        <div class="ad-space">
            <a href="product_page.php?id=11">
                <img src=/images/LEBRON.png alt="Advertisement" style="width: 100%; height: 100%;">
        </div>

        <!-- Top 5 Bestsellers Section -->
        <div class="bestsellers-section">
            <h2>Top 5 Bestsellers</h2>
            <div class="bestsellers-container">
                <?php if (!empty($bestsellers)): ?>
                    <?php foreach ($bestsellers as $product): ?>
                        <div class="bestseller-item">
                            <a href="product_page.php?id=<?php echo $product['id']; ?>">
                                <img src="/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p>RM <?php echo number_format($product['price'], 2); ?></p>
                            </a>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No bestsellers found.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <?php include '_foot.php'; ?>

</body>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    /* Ad Space Styles */
    .ad-space {
        margin-top: 20px;
        width: fit-content;
        height: fit-content;
        text-align: center;
    }

    .ad-space img {
        width: 100%;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Bestsellers Section Styles */
    .bestsellers-section {
        margin-top: 20px;
        margin-bottom: 20px;
        height: 400px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .bestsellers-section h2 {
        text-align: center;
        font-size: 32px;
        margin-bottom: 30px;
        font-family: 'Montserrat', sans-serif;
    }

    .bestsellers-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .bestseller-item {
        width: 18%;
        margin-bottom: 20px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .bestseller-item:hover {
        transform: scale(1.05, 1.05);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    }

    .bestseller-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .bestseller-item h3 {
        font-size: 18px;
        margin: 10px 0;
        color: #333;
        font-family: 'Montserrat', sans-serif;
    }

    .bestseller-item p {
        font-size: 16px;
        color: #666;
        margin-bottom: 10px;
        font-family: 'Montserrat', sans-serif;
    }

    .bestseller-item a {
        text-decoration: none;
        color: white;
        font-family: 'Montserrat', sans-serif;
    }

    @keyframes fadeInOut {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }

        10% {
            opacity: 1;
            transform: translateY(0);
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    @media (max-width: 768px) {
        .bestseller-item {
            width: 45%;
        }
    }

    @media (max-width: 480px) {
        .bestseller-item {
            width: 100%;
        }
    }
</style>

</html>