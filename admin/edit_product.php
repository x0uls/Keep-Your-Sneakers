<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

require '../db.php';

if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$product_id = intval($_GET['id']);

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Fetch categories
$allCategories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$categoryStmt = $pdo->prepare("SELECT category_id FROM product_categories WHERE product_id = ?");
$categoryStmt->execute([$product_id]);
$currentCategory = $categoryStmt->fetchColumn();

// Fetch sizes and stock
$sizeStockStmt = $pdo->prepare("
    SELECT ps.id as ps_id, s.id as size_id, s.size_label, ps.stock
    FROM product_sizes ps
    JOIN sizes s ON ps.size_id = s.id
    WHERE ps.product_id = ?
    ORDER BY s.size_label
");
$sizeStockStmt->execute([$product_id]);
$sizes = $sizeStockStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all size options
$allSizes = $pdo->query("SELECT * FROM sizes ORDER BY size_label")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product - <?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }

        .admin-container {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 20px;
            font-weight: 600;
        }

        input[type="text"],
        textarea,
        select,
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .size-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }

        .size-row select,
        .size-row input {
            flex: 1;
        }

        .add-size-btn {
            margin-top: 20px;
            display: inline-block;
            background: black;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 999px;
            cursor: pointer;
            font-weight: bold;
        }

        .save-btn {
            margin-top: 30px;
            width: 100%;
            padding: 14px;
            background: black;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .image-preview {
            max-width: 100%;
            border-radius: 12px;
            margin-top: 15px;
        }

        .floating-return {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: #111;
            /* Green color, similar to a 'back' button */
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .floating-return:hover {
            background: #333;
            /* Darker green when hovered */
        }
    </style>
</head>

<body>

    <div class="admin-container">
        <h2>Edit Product</h2>

        <form method="POST" action="update_product.php" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

            <label>Product Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label>Description</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>

            <label>Price (RM)</label>
            <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

            <label>Category</label>
            <select name="category_id">
                <option value="">Select Category</option>
                <?php foreach ($allCategories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $currentCategory) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Product Image</label>
            <img src="/products/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="image-preview">

            <input type="file" name="product_image" accept="image/*">

            <label>Sizes & Stock</label>
            <div id="sizes-container">
                <?php foreach ($sizes as $s): ?>
                    <div class="size-row">
                        <select name="sizes[<?= $s['ps_id'] ?>][size_id]">
                            <?php foreach ($allSizes as $opt): ?>
                                <option value="<?= $opt['id'] ?>" <?= ($opt['id'] == $s['size_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($opt['size_label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="sizes[<?= $s['ps_id'] ?>][stock]" value="<?= $s['stock'] ?>" min="0" placeholder="Stock">
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="add-size-btn" onclick="addSizeRow()">+ Add Size</button>

            <button type="submit" class="save-btn">Save Changes</button>
        </form>

    </div>

    <a href="product_manage.php" class="floating-return">← Back to Product Manage</a>

</body>

</html>