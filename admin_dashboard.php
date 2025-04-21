<?php
// admin_dashboard.php

include 'db.php';

// Fetch products
$products = $pdo->query("
    SELECT id, name, price, image
    FROM products
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Helvetica Neue', sans-serif;
            background-color: #f5f5f5;
            color: #111;
        }

        header {
            background-color: #111;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.8em;
            letter-spacing: 1px;
        }

        main {
            padding: 40px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            color: inherit;
            overflow: hidden;
        }

        .product-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }

        .product-info {
            padding: 20px;
        }

        .product-info h3 {
            font-size: 1.1em;
            margin: 0 0 10px;
        }

        .product-info p {
            margin: 0;
            font-weight: bold;
            color: #555;
        }

        .insert-button {
            margin-bottom: 30px;
            display: inline-block;
            background: #111;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .insert-button:hover {
            background: #333;
        }

        .floating-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #111;
            color: white;
            border-radius: 50%;
            font-size: 36px;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .floating-button:hover {
            background-color: #333;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <a href="insert_product.php" class="floating-button" title="Insert Product">+</a>

    <main>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <a href="Admin_Product_Page.php?id=<?= $product['id'] ?>" class="product-card">
                    <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p>RM <?= number_format($product['price'], 2) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

</body>

</html>