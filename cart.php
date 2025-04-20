<?php
session_start();
require 'db.php';

// ✅ Handle AJAX remove request BEFORE any HTML or includes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    header('Content-Type: application/json'); // Tell browser it's JSON

    if (isset($_POST['product_id'], $_SESSION['user_id'])) {
        $product_id = intval($_POST['product_id']);
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $success = $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// ✅ Only include head after AJAX handling
include '_head.php';

$total_price = 0;
$cart_items = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT c.product_id, c.quantity, p.name, p.price, p.image, p.stock 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <h2>Shopping Cart</h2>

    <div id="cartContainer">
        <?php if (!empty($cart_items)): ?>
            <table style="border-collapse: collapse; width: 100%;" id="cartTable">
                <tr>
                    <th style="border: 1px solid #ccc; padding: 8px;">Select</th>
                    <th style="border: 1px solid #ccc; padding: 8px;">Image</th>
                    <th style="border: 1px solid #ccc; padding: 8px;">Name</th>
                    <th style="border: 1px solid #ccc; padding: 8px;">Price</th>
                    <th style="border: 1px solid #ccc; padding: 8px;">Quantity</th>
                    <th style="border: 1px solid #ccc; padding: 8px;">Total</th>
                    <th style="border: 1px solid #ccc; padding: 8px;">Action</th>
                </tr>

                <?php foreach ($cart_items as $item): ?>
                    <?php
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                    ?>
                    <tr data-id="<?php echo $item['product_id']; ?>">
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: center;">
                            <input type="checkbox" class="item-checkbox" data-price="<?php echo $item['price']; ?>" value="<?php echo $item['product_id']; ?>" checked>
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px;">
                            <img src="<?php echo $item['image']; ?>" alt="Product" style="width: 80px;">
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px;"><?php echo $item['name']; ?></td>
                        <td style="border: 1px solid #ccc; padding: 8px;">RM<?php echo number_format($item['price'], 2); ?></td>
                        <td style="border: 1px solid #ccc; padding: 8px;">
                            <input type="number" class="quantity-input" data-price="<?php echo $item['price']; ?>"
                                value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" style="width: 60px;">
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px;" class="item-total">
                            RM<?php echo number_format($item_total, 2); ?>
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px;">
                            <button class="remove-btn" data-id="<?php echo $item['product_id']; ?>">❌</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>Total Price: RM<span id="totalPrice"><?php echo number_format($total_price, 2); ?></span></h3>

            <a href="checkout.php" id="checkoutBtn">Proceed to Checkout</a>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script src="js/cart.js"></script>

</body>

</html>

<?php include '_foot.php'; ?>