<?php
session_start();
require 'db.php';  // Ensure PDO connection is established at the beginning
include '_head.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: LogInPage.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total_price = 0;
$cart_items = [];

// Get items from DB cart using PDO
$sql = "SELECT c.product_id, c.quantity, p.name, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #444;
            padding: 12px;
            text-align: center;
        }

        img {
            width: 80px;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2 class="center">Checkout</h2>

    <?php if (!empty($cart_items)): ?>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price (RM)</th>
                <th>Quantity</th>
                <th>Total (RM)</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><img src="<?php echo $item['image']; ?>" alt="Product"></td>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['price']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo $item['price'] * $item['quantity']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3 class="center">Total Price: RM<?php echo number_format($total_price, 2); ?></h3>
        <div class="center">
            <form action="process_checkout.php" method="POST">
                <button type="submit">Place Order</button>
            </form>
        </div>
    <?php else: ?>
        <p class="center">Your cart is empty.</p>
    <?php endif; ?>

</body>

</html>

<?php include '_foot.php'; ?>