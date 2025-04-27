<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../db.php'; // Make sure your db.php connects to your database properly

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $categories = $_POST['categories'] ?? [];
    $sizes = $_POST['sizes'] ?? [];

    // Handle image upload
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $uploadPath = '../products/' . basename($imageName);

    if (!move_uploaded_file($imageTmp, $uploadPath)) {
        die('Image upload failed.');
    }

    try {
        $pdo->beginTransaction();

        // Insert product
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $imageName]);
        $productId = $pdo->lastInsertId();

        // Insert product_categories
        $catStmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
        foreach ($categories as $catId) {
            $catStmt->execute([$productId, $catId]);
        }

        // Prepare size insertions
        $catMapStmt = $pdo->query("SELECT id, name FROM categories");
        $categoryMap = [];
        foreach ($catMapStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $categoryMap[$row['name']] = $row['id'];
        }

        $sizeLookup = $pdo->prepare("SELECT id FROM sizes WHERE category_id = ? AND size_label = ?");
        $sizeInsert = $pdo->prepare("INSERT INTO product_sizes (product_id, size_id, stock) VALUES (?, ?, ?)");

        foreach ($sizes as $categoryName => $sizeArray) {
            if (!isset($categoryMap[$categoryName])) continue;

            $categoryId = $categoryMap[$categoryName];

            foreach ($sizeArray as $sizeLabel => $stock) {
                if ($stock !== '' && is_numeric($stock)) {
                    $sizeLookup->execute([$categoryId, $sizeLabel]);
                    $sizeId = $sizeLookup->fetchColumn();

                    if ($sizeId) {
                        $sizeInsert->execute([$productId, $sizeId, $stock]);
                    }
                }
            }
        }

        $pdo->commit();
        $success = "Product successfully added!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/css/admin.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>
        <h3>Add Product</h3>

        <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" required />

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" required />

            <label>Image:</label>
            <input type="file" name="image" required />

            <label>Categories:</label>
            <div class="checkbox-group">
                <div>
                    <label><input type="checkbox" name="categories[]" value="1" class="category-toggle" data-target="#sizes-men" /> Men</label>
                    <div id="sizes-men" class="category-section sizes-group" style="display:none;">
                        <h4>Men Sizes</h4>
                        <?php
                        $menSizes = ['UK 6 (EU 40)', 'UK 6.5', 'UK 7', 'UK 7.5', 'UK 8', 'UK 8.5', 'UK 9', 'UK 9.5', 'UK 10', 'UK 10.5', 'UK 11', 'UK 12'];
                        foreach ($menSizes as $size) {
                            echo "<div class='size-row'>$size <input type='number' name='sizes[Men][$size]' min='0' /></div>";
                        }
                        ?>
                    </div>
                </div>

                <div>
                    <label><input type="checkbox" name="categories[]" value="2" class="category-toggle" data-target="#sizes-women" /> Women</label>
                    <div id="sizes-women" class="category-section sizes-group" style="display:none;">
                        <h4>Women Sizes</h4>
                        <?php
                        $womenSizes = ['UK 2.5', 'UK 3', 'UK 3.5', 'UK 4', 'UK 4.5', 'UK 5', 'UK 5.5', 'UK 6', 'UK 6.5', 'UK 7', 'UK 7.5'];
                        foreach ($womenSizes as $size) {
                            echo "<div class='size-row'>$size <input type='number' name='sizes[Women][$size]' min='0' /></div>";
                        }
                        ?>
                    </div>
                </div>

                <div>
                    <label><input type="checkbox" name="categories[]" value="3" class="category-toggle" data-target="#sizes-kids" /> Kids</label>
                    <div id="sizes-kids" class="category-section sizes-group" style="display:none;">
                        <h4>Kids Sizes</h4>
                        <?php
                        $kidsSizes = ['UK 3', 'UK 3.5', 'UK 4', 'UK 4.5', 'UK 5', 'UK 5.5', 'UK 6 (EU 39)', 'UK 6 (EU 40)'];
                        foreach ($kidsSizes as $size) {
                            echo "<div class='size-row'>$size <input type='number' name='sizes[Kids][$size]' min='0' /></div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <input type="submit" value="Add Product" />
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('.category-toggle').on('change', function() {
                const target = $($(this).data('target'));
                if ($(this).is(':checked')) {
                    target.slideDown();
                } else {
                    target.slideUp();
                    target.find('input[type=number]').val('');
                }
            });
        });
    </script>
</body>

</html>