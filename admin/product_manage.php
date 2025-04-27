<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../db.php'; // Database connection

// Success and error messages
$message = '';
$message_type = '';

// Handle delete product
if (isset($_REQUEST['delete_id'])) {
    $product_id = $_REQUEST['delete_id'];

    try {
        $pdo->beginTransaction();

        // You should also delete from related tables if necessary (e.g., product_sizes, product_categories)
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);

        $pdo->commit();
        $message = "Product deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Error deleting product: " . $e->getMessage();
        $message_type = "error";
    }

    header("Location: product_manage.php?message=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        $searchTerm = '%' . $search . '%';
        $stmt = $pdo->prepare("
            SELECT p.id, p.name, p.price, p.image, c.name AS category_name
            FROM products p
            LEFT JOIN product_categories pc ON p.id = pc.product_id
            LEFT JOIN categories c ON pc.category_id = c.id
            WHERE p.name LIKE ?
            ORDER BY p.id DESC
        ");
        $stmt->execute([$searchTerm]);
    } else {
        $stmt = $pdo->query("
            SELECT p.id, p.name, p.price, p.image, c.name AS category_name
            FROM products p
            LEFT JOIN product_categories pc ON p.id = pc.product_id
            LEFT JOIN categories c ON pc.category_id = c.id
            ORDER BY p.id DESC
        ");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching products: " . $e->getMessage();
    $message_type = "error";
}

// Check for message in URL parameters
if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
    $message_type = $_GET['type'] ?? 'info';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #111;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .button-container a {
            padding: 10px 20px;
            background-color: #111;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .button-container a:hover {
            background-color: gray;
        }

        .delete-button {
            background: none;
            border: none;
            color: red;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            font-size: inherit;
            font-family: inherit;
        }

        .delete-button:hover {
            color: darkred;
            text-decoration: underline;
        }

        .floating-return {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: #111;
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
            background: gray;
        }

        /* Message styles */
        .message {
            padding: 15px;
            margin: 20px auto;
            max-width: 80%;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .info {
            background-color: #d9edf7;
            color: #31708f;
            border: 1px solid #bce8f1;
        }

        .product-image {
            width: 100px;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .search-bar button {
            padding: 10px 15px;
            background-color: #111;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        .search-bar button:hover {
            background-color: #333;
        }
    </style>
</head>

<body>

    <h2>Product Management</h2>

    <!-- Search Bar -->
    <div class="search-bar">
        <form action="product_manage.php" method="GET">
            <input type="text" name="search" placeholder="Search product name..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Display messages -->
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Products Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></td>
                        <td>RM <?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php if (!empty($product['image'])): ?>
                                <img src="../products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                            <form action="product_manage.php" method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No products found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Insert Product Button -->
    <div class="button-container">
        <a href="insert_product.php">Insert Product</a>
    </div>

    <!-- Floating Back to Dashboard Button -->
    <a href="admin_dashboard.php" class="floating-return">← Back to Dashboard</a>

    <script src="../js/admin.js"></script>

</body>

</html>