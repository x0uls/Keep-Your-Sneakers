<?php
session_start();
include '_head.php';
include 'db.php';

// Restrict access to admins only
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: LogInPage.php");
    exit();
}

// Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $description, $price, $target_file);
    $stmt->execute();
    $stmt->close();
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch products
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/app.css">
</head>

<body>
    <h2>Admin Dashboard</h2>
    <h3>Add Product</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required><br>
        <textarea name="description" placeholder="Description" required></textarea><br>
        <input type="number" step="0.01" name="price" placeholder="Price" required><br>
        <input type="file" name="image" accept="image/*" required><br>
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h3>Existing Products</h3>
    <table border="1">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><img src="<?php echo $row['image']; ?>" width="50"></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>$<?php echo $row['price']; ?></td>
                <td><a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a></td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>

<?php
$conn->close();
?>