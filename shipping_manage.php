<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: LogInPage.php");
    exit();
}

include 'db.php'; // Database connection

// Success and error messages
$message = '';
$message_type = '';

// Update shipping status logic
if (isset($_POST['update_shipping'])) {
    $order_id = $_POST['order_id'];
    $shipping_status = $_POST['shipping_status'];

    try {
        $stmt = $pdo->prepare("UPDATE orders SET shipping_status = ? WHERE id = ?");
        $stmt->execute([$shipping_status, $order_id]);
        $message = "Shipping status updated successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error updating shipping status: " . $e->getMessage();
        $message_type = "error";
    }
}

// Delete order logic with transaction
if (isset($_REQUEST['delete_id'])) {
    $order_id = $_REQUEST['delete_id'];

    try {
        $pdo->beginTransaction();

        // First delete all order items
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Then delete the order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);

        $pdo->commit();
        $message = "Order deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Error deleting order: " . $e->getMessage();
        $message_type = "error";
    }

    // Refresh after delete to prevent form resubmission
    header("Location: shipping_manage.php?message=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

// Fetch orders with their shipping status and username
try {
    $stmt = $pdo->query("SELECT orders.id, orders.order_date, orders.payment_status, orders.shipping_status, 
                         orders.address, users.username 
                         FROM orders 
                         LEFT JOIN users ON orders.user_id = users.id 
                         ORDER BY orders.id DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching orders: " . $e->getMessage();
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
    <title>Shipping Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
            background-color: #333;
        }

        .shipping-status-dropdown {
            padding: 8px;
            font-size: 14px;
            width: 100px;
            border-radius: 6px;
        }

        .update-button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .update-button:hover {
            background-color: #45a049;
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
            background: #333;
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
    </style>
</head>

<body>

    <h2>Shipping Management</h2>

    <!-- Display messages -->
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>" id="message-box">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Username</th>
                <th>Shipping Status</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <td>
                            <form action="shipping_manage.php" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="shipping_status" class="shipping-status-dropdown">
                                    <option value="Pending" <?php echo $order['shipping_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Shipped" <?php echo $order['shipping_status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $order['shipping_status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Canceled" <?php echo $order['shipping_status'] == 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                                </select>
                                <button type="submit" name="update_shipping" class="update-button">Update</button>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                        <td>
                            <!-- Delete Action -->
                            <form action="shipping_manage.php" method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this order and all its items?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No orders found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Floating Back to Dashboard Button -->
    <a href="admin_dashboard.php" class="floating-return">← Back to Dashboard</a>

    <!-- JavaScript to hide the message box after 3 seconds -->
    <script>
        setTimeout(function() {
            var messageBox = document.getElementById("message-box");
            if (messageBox) {
                messageBox.style.display = "none";
            }
        }, 5000);
    </script>

</body>

</html>