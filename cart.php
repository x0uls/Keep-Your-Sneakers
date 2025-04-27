<?php
session_start();
require 'db.php';  // Ensure PDO connection is established at the beginning

// ✅ Handle AJAX update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    header('Content-Type: application/json');

    if (isset($_POST['product_id'], $_POST['size_id'], $_POST['quantity'], $_SESSION['user_id'])) {
        $product_id = intval($_POST['product_id']);
        $size_id = intval($_POST['size_id']);
        $quantity = intval($_POST['quantity']);
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id");
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':size_id', $size_id);
        $success = $stmt->execute();

        echo json_encode(['success' => $success]);
        exit;
    }
    echo json_encode(['success' => false, 'error' => 'Invalid update']);
    exit;
}

// ✅ Handle AJAX remove request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    header('Content-Type: application/json');

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

    echo json_encode(['success' => false, 'error' => 'Invalid remove request']);
    exit;
}

// ✅ Only include head after AJAX handling
include '_head.php';

$total_price = 0;
$cart_items = [];
$stock_warnings = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch cart items with product info and stock
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

    // Loop through items and adjust quantity if needed
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $max_stock = intval($row['stock']);
        $current_qty = intval($row['quantity']);

        if ($current_qty > $max_stock) {
            // Update DB to max stock
            $update_stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id AND size_id = :size_id");
            $update_stmt->execute([
                ':quantity' => $max_stock,
                ':user_id' => $user_id,
                ':product_id' => $row['product_id'],
                ':size_id' => $row['size_id'],
            ]);

            $row['quantity'] = $max_stock;

            $stock_warnings[] = "Your item \"{$row['name']}\" (Size {$row['size_label']}) was reduced to {$max_stock} due to stock limits.";
        }

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

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        #cartContainer {
            font-family: 'Poppins', sans-serif;
            color: #111;
            background-color: #f5f5f5;
            padding: 40px 20px;
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
            margin-top: 30px;
        }

        h2 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }

        .warning-box {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .warning-box ul {
            margin: 0;
            padding-left: 20px;
        }

        #cartItems {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding: 10px 0;
        }

        .cart-item-info {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .cart-item-info img {
            border-radius: 12px;
        }

        .cart-item-info h4 {
            margin: 0;
            font-weight: 600;
        }

        .cart-item-price,
        .cart-item-quantity,
        .cart-item-total {
            display: flex;
            align-items: center;
        }

        .cart-item-quantity {
            gap: 10px;
        }

        .quantity-wrapper button {
            background: #111;
            color: white;
            border: none;
            width: 34px;
            height: 34px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
        }

        .quantity-wrapper button:hover {
            background: #333;
        }

        .quantity-display {
            border: none;
            background: transparent;
            width: 30px;
            text-align: center;
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        #checkoutBtn {
            display: inline-block;
            background: #111;
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        #checkoutBtn:hover {
            background: #333;
        }

        @media (max-width: 768px) {
            #cartItems {
                flex-direction: column;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <div id="cartContainer">
        <h2>Shopping Cart</h2>

        <?php if (!empty($stock_warnings)): ?>
            <div class="warning-box">
                <ul>
                    <?php foreach ($stock_warnings as $warning): ?>
                        <li><?php echo htmlspecialchars($warning); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($cart_items)): ?>
            <div id="cartItems">
                <?php foreach ($cart_items as $item): ?>
                    <?php
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                    ?>
                    <div class="cart-item" data-id="<?php echo $item['product_id']; ?>" data-size="<?php echo $item['size_id']; ?>">
                        <div class="cart-item-info">
                            <img src="/products/<?php echo htmlspecialchars($item['image']); ?>" alt="Product" style="width: 80px;">
                            <div>
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <small>Size: <?php echo htmlspecialchars($item['size_label']); ?></small>
                            </div>
                        </div>
                        <div class="cart-item-price">
                            <p>RM<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <div class="cart-item-quantity">
                            <div class="quantity-wrapper"
                                data-product="<?php echo $item['product_id']; ?>"
                                data-size="<?php echo $item['size_id']; ?>"
                                data-max="<?php echo $item['stock']; ?>">
                                <button class="qty-minus"><?php echo $item['quantity'] > 1 ? '−' : '🗑️'; ?></button>
                                <input type="text" class="quantity-display" value="<?php echo htmlspecialchars($item['quantity']); ?>" readonly>
                                <button class="qty-plus">+</button>
                            </div>
                        </div>
                        <div class="cart-item-total">
                            <p>RM<?php echo number_format($item_total, 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

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