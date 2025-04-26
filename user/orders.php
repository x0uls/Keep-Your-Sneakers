<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../_head.php';
include '../db.php';

// Fetch orders for the user
$stmt = $pdo->prepare("SELECT id, shipping_status FROM orders WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .orders-container {
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
            align-items: flex-start;
        }

        /* Sidebar (Button Container) */
        .button-container {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 200px;
            display: flex;
            flex-direction: column;
            height: auto;
            min-height: 100px;
        }

        .button-container a {
            display: block;
            padding: 12px;
            color: #333;
            text-decoration: none;
            text-align: center;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 12px;
            background-color: #fff;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .button-container a:hover {
            background-color: #ddd;
        }

        /* Orders Section */
        .orders-wrapper {
            background-color: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        .orders-wrapper h2 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 24px;
            color: #111;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item span {
            font-size: 16px;
            color: #555;
        }

        .order-item .order-status {
            font-weight: 500;
        }

        .order-item .view-button {
            background-color: #111;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .order-item .view-button:hover {
            background-color: #333;
        }
    </style>
</head>

<body>

    <div class="orders-container">
        <div class="main-container">
            <!-- Sidebar (Button Container) -->
            <div class="button-container">
                <a href="dashboard.php">Dashboard</a>
                <a href="orders.php">Orders</a>
            </div>

            <!-- Orders Section -->
            <div class="orders-wrapper">
                <h2>Your Orders</h2>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-item">
                            <span>Order #<?php echo htmlspecialchars($order['id']); ?></span>
                            <span class="order-status"><?php echo htmlspecialchars($order['shipping_status']); ?></span>
                            <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="view-button">View Order</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '_foot.php'; ?>
</body>

</html>