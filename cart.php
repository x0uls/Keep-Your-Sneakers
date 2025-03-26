<?php
session_start();
require 'db.php';
include '_head.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = $_SESSION['cart'];
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
</head>

<body>

    <h2>Shopping Cart</h2>

    <?php if (!empty($cart_items)): ?>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart_items as $id => $item):
                $item_total = $item['price'] * $item['quantity'];
                $total_price += $item_total;
            ?>
                <tr>
                    <td><img src="<?php echo $item['image']; ?>" alt="Product"></td>
                    <td><?php echo $item['name']; ?></td>
                    <td>RM<?php echo $item['price']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>RM<?php echo $item_total; ?></td>
                    <td><a class="remove-btn" href="remove_from_cart.php?id=<?php echo $id; ?>">❌</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total Price: RM<?php echo $total_price; ?></h3>

        <a href="checkout.php">Proceed to Checkout</a>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

</body>

</html>
<?php include '_foot.php'; // Include footer 
?>