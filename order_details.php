<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: LogInPage.php");
    exit();
}

include '_head.php';
include 'db.php';

// Get the order_id from the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details from the database
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id");
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Fetch order items (assuming you have a table for order items)
    // Fetch order details along with product name and size
    $stmt = $pdo->prepare("
SELECT oi.quantity, oi.price, p.name, s.size_label
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN sizes s ON oi.size_id = s.id
WHERE oi.order_id = :order_id
");
    $stmt->bindParam(':order_id', $_GET['order_id'], PDO::PARAM_INT);
    $stmt->execute();
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} else {
    // If no order_id is provided, redirect to orders page
    header("Location: orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .order-details-container {
            font-family: 'Poppins', sans-serif;
            padding: 60px 20px;
            display: flex;
            justify-content: center;
            background-color: #f5f5f5;
        }

        .main-container {
            display: flex;
            gap: 20px;
            max-width: 1100px;
            width: 100%;
            flex-direction: column;
        }

        .order-info {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }

        .order-info h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .order-details-table {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .order-details-table table {
            width: 80%;
            border-collapse: collapse;
            text-align: center;
        }

        .order-details-table th,
        .order-details-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .order-details-table th {
            background-color: #f8f8f8;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .button-container a {
            padding: 12px 30px;
            background-color: #111;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .button-container a:hover {
            background-color: #333;
        }
    </style>
</head>

<body>

    <div class="order-details-container">
        <div class="main-container">
            <div class="order-info">
                <h2>Order #<?php echo htmlspecialchars($order['id']); ?></h2>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['shipping_status']); ?></p>
                <p><strong>Placed on:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
            </div>

            <h3 style="text-align: center;">Order Items</h3>
            <div class="order-details-table">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($order_items) > 0): ?>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['size_label']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td><?php echo number_format($item['price'], 2); ?> USD</td>
                                    <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?> USD</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No items found in this order.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="button-container">
                <a href="orders.php">Back to Orders</a>
            </div>
        </div>
    </div>

    <?php include '_foot.php'; ?>
</body>

</html>