<?php
session_start();
require 'db.php';  // Ensure PDO connection is established at the beginning

// ✅ Handle AJAX remove request BEFORE any HTML or includes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    header('Content-Type: application/json'); // Tell browser it's JSON

    if (isset($_POST['product_id'], $_POST['size_id'], $_SESSION['user_id'])) {
        $product_id = intval($_POST['product_id']);
        $size_id = intval($_POST['size_id']);
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':size_id', $size_id);
        $success = $stmt->execute();

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

    // Fetch cart items with PDO
    $sql = "SELECT 
            c.product_id, c.size_id, c.quantity, 
            p.name, p.price, p.image, 
            s.size_label, ps.stock 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        JOIN product_sizes ps ON ps.product_id = c.product_id AND ps.size_id = c.size_id
        JOIN sizes s ON c.size_id = s.id
        WHERE c.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch results and populate $cart_items
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cart_items[] = $row;
    }
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
                    <tr data-id="<?php echo $item['product_id']; ?>" data-size="<?php echo $item['size_id']; ?>">
                        <td style="text-align: center;">
                            <input type="checkbox" class="item-checkbox"
                                data-price="<?php echo htmlspecialchars($item['price']); ?>"
                                value="<?php echo $item['product_id'] . '-' . $item['size_id']; ?>" checked>
                        </td>
                        <td><img src="/products/<?php echo htmlspecialchars($item['image']); ?>" alt="Product" style="width: 80px;"></td>
                        <td>
                            <?php echo htmlspecialchars($item['name']); ?><br>
                            <small>Size: <?php echo htmlspecialchars($item['size_label']); ?></small>
                        </td>
                        <td>RM<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="quantity-wrapper"
                                data-product="<?php echo $item['product_id']; ?>"
                                data-size="<?php echo $item['size_id']; ?>"
                                data-max="<?php echo $item['stock']; ?>">
                                <button class="qty-minus"><?php echo $item['quantity'] > 1 ? '−' : '🗑️'; ?></button>
                                <input type="text" class="quantity-display" value="<?php echo htmlspecialchars($item['quantity']); ?>" readonly>
                                <button class="qty-plus">+</button>
                            </div>
                        </td>
                        <td class="item-total">
                            RM<?php echo number_format($item_total, 2); ?>
                        </td>
                        <td></td> <!-- blank cell where ❌ used to be -->

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