<?php
include 'db.php';

// Handle stock, price, and category update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product_id'])) {
    $productId = $_POST['update_product_id'];
    $newPrice = $_POST['price'];
    $newCategory = $_POST['category'];  // New category selection

    try {
        $pdo->beginTransaction();

        // Update price
        $updatePrice = $pdo->prepare("UPDATE products SET price = ? WHERE id = ?");
        $updatePrice->execute([$newPrice, $productId]);

        // Update stock per size
        if (isset($_POST['stock']) && is_array($_POST['stock'])) {
            $updateStock = $pdo->prepare("UPDATE product_sizes SET stock = ? WHERE id = ?");
            foreach ($_POST['stock'] as $ps_id => $qty) {
                $updateStock->execute([$qty, $ps_id]);
            }
        }

        // Add new sizes if needed
        if (isset($_POST['new_sizes']) && is_array($_POST['new_sizes'])) {
            $insertStock = $pdo->prepare("INSERT INTO product_sizes (product_id, size_id, stock) VALUES (?, ?, ?)");
            foreach ($_POST['new_sizes'] as $sizeId => $stock) {
                if ($stock > 0) {
                    $insertStock->execute([$productId, $sizeId, $stock]);
                }
            }
        }

        // Handle category updates
        if ($newCategory) {
            // Check if the category is already associated
            $categoryCheckStmt = $pdo->prepare("
                SELECT * FROM product_categories WHERE product_id = ? AND category_id = ?
            ");
            $categoryCheckStmt->execute([$productId, $newCategory]);
            $categoryExists = $categoryCheckStmt->fetchColumn();

            if (!$categoryExists) {
                // Insert category if it's not already associated
                $insertCategoryStmt = $pdo->prepare("
                    INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)
                ");
                $insertCategoryStmt->execute([$productId, $newCategory]);
            }
        }

        $pdo->commit();
        $message = "Product updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Update failed: " . $e->getMessage();
    }
}

// Fetch products
$products = $pdo->query("
    SELECT p.id, p.name, p.price, p.image
    FROM products p
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all available sizes
$sizes = $pdo->query("SELECT id, size_label FROM sizes ORDER BY size_label")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories associated with each product
$categoriesByProduct = [];
$categoryAssoc = $pdo->query("
    SELECT pc.product_id, c.id AS category_id, c.name AS category_name
    FROM product_categories pc
    JOIN categories c ON pc.category_id = c.id
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($categoryAssoc as $category) {
    $categoriesByProduct[$category['product_id']][] = $category;
}

// Fetch sizes with stock for each product
$productSizes = $pdo->query("
    SELECT ps.id AS ps_id, ps.product_id, ps.size_id, ps.stock, s.size_label
    FROM product_sizes ps
    JOIN sizes s ON ps.size_id = s.id
    ORDER BY s.size_label
")->fetchAll(PDO::FETCH_ASSOC);

$sizesByProduct = [];
foreach ($productSizes as $row) {
    $sizesByProduct[$row['product_id']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Product Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .details {
            display: none;
            background: #f9f9f9;
        }

        .update-btn {
            float: right;
            margin-top: 10px;
        }

        input[type='number'] {
            width: 60px;
        }

        tr.clickable {
            cursor: pointer;
            background: #f1f1f1;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <h2>Admin Dashboard</h2>

    <?php if (isset($message)) echo "<p style='color: green;'>$message</p>"; ?>

    <a href="insert_product.php">+ Insert New Product</a>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Price (Editable)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr class="clickable" data-target="#details-<?= $p['id'] ?>">
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><img src="uploads/<?= htmlspecialchars($p['image']) ?>" width="50"></td>
                    <td>RM <input type="number" name="price" value="<?= $p['price'] ?>" data-pid="<?= $p['id'] ?>"></td>
                </tr>
                <tr class="details" id="details-<?= $p['id'] ?>">
                    <td colspan="3">
                        <form method="POST">
                            <input type="hidden" name="update_product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="price" id="price-input-<?= $p['id'] ?>" value="<?= $p['price'] ?>">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Size</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Editable Category Dropdown -->
                                    <tr>
                                        <td>
                                            <!-- Category Dropdown -->
                                            <select name="category" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>"
                                                        <?php if (in_array($category['id'], array_column($categoriesByProduct[$p['id']], 'category_id'))) echo 'selected'; ?>>
                                                        <?= $category['name'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <!-- Size Dropdown -->
                                            <select name="new_sizes[0]" required>
                                                <option value="">Select Size</option>
                                                <?php foreach ($sizes as $size): ?>
                                                    <option value="<?= $size['id'] ?>"><?= $size['size_label'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <!-- Stock Input -->
                                            <input type="number" name="new_sizes[0_stock]" value="0" min="0" placeholder="Stock">
                                        </td>
                                    </tr>

                                    <?php if (!empty($sizesByProduct[$p['id']])): ?>
                                        <?php foreach ($sizesByProduct[$p['id']] as $size): ?>
                                            <tr>
                                                <td>
                                                    <!-- Display categories for this product -->
                                                    <?php
                                                    // Display all categories for this product
                                                    if (isset($categoriesByProduct[$p['id']])) {
                                                        echo implode(', ', array_column($categoriesByProduct[$p['id']], 'category_name'));
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= htmlspecialchars($size['size_label']) ?></td>
                                                <td>
                                                    <input type="number" name="stock[<?= $size['ps_id'] ?>]" value="<?= $size['stock'] ?>" min="0">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('.clickable').click(function() {
                const target = $(this).data('target');
                $(target).slideToggle();
            });

            // When price input changes, also update hidden input inside the form
            $("input[name='price']").on('input', function() {
                const productId = $(this).data('pid');
                const value = $(this).val();
                $('#price-input-' + productId).val(value);
            });
        });
    </script>

</body>

</html>