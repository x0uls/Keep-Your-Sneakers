<?php
session_start();
include 'db.php';

// Check if user is an admin (Modify this based on your login system)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: LogInPage.php");
    exit();
}

// Handle Product Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Handle Image Upload
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $name, $description, $price, $target_file, $stock);
        $stmt->execute();
        $stmt->close();
        echo "Product added successfully!";
    } else {
        echo "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Add Product</title>
</head>

<body>
    <h2>Add a New Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" required><br>

        <label>Description:</label>
        <textarea name="description" required></textarea><br>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" required><br>

        <label>Stock:</label>
        <input type="number" name="stock" required><br>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*" required><br>

        <button type="submit">Add Product</button>
    </form>
</body>

</html>

<h2>Existing Products</h2>
<table border="1">
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Action</th>
    </tr>

    <?php
    $result = $conn->query("SELECT * FROM products");
    while ($row = $result->fetch_assoc()):
    ?>
        <tr>
            <td><img src="<?= $row['image'] ?>" width="100"></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>$<?= $row['price'] ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
                <form action="delete_product.php" method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>