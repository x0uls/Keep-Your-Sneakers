<?php
session_start();
include '_head.php';
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle Product Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $stock = intval($_POST['stock']);
    $created_at = date("Y-m-d H:i:s");

    // Handle Image Upload
    $target_dir = "uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image, stock, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssis", $name, $description, $price, $category, $target_file, $stock, $created_at);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Product added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error adding product.</p>";
    }
    $stmt->close();
}

// Handle Product Deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Product deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error deleting product.</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>

<body>

    <h2>Admin Dashboard</h2>

    <!-- Add Product Form -->
    <h3>Add Product</h3>
    <form method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required><br>

        <label>Category:</label><br>
        <select name="category" required>
            <option value="Men">Men</option>
            <option value="Women">Women</option>
            <option value="Kids">Kids</option>
            <option value="Sales">Sales</option>
        </select><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" required><br>

        <label>Image:</label><br>
        <input type="file" name="image" required><br>

        <input type="submit" name="add_product" value="Add Product">
    </form>

    <!-- Display Products -->
    <h3>Product List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM products");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['description']}</td>
            <td>{$row['price']}</td>
            <td>{$row['category']}</td>
            <td>{$row['stock']}</td>
            <td><img src='{$row['image']}' width='50'></td>
            <td><a href='admin_dashboard.php?delete={$row['id']}'>Delete</a></td>
        </tr>";
        }
        ?>
    </table>

</body>

</html>
<?php include '_foot.php'; ?>