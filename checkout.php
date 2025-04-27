<?php
session_start();
require 'db.php';
include '_head.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total_price = 0;
$cart_items = [];

// Fetch cart items
$sql = "SELECT 
            c.product_id, 
            c.quantity, 
            c.size_id,
            s.size_label, 
            p.name, 
            p.price, 
            p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        JOIN sizes s ON c.size_id = s.id 
        WHERE c.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

// Fetch user addresses
$sql_addresses = "SELECT * FROM addresses WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql_addresses);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['address_id'])) {
        $_SESSION['address_id'] = $_POST['address_id']; // ✅ Fix: Set in session
    }

    $payment_method = $_POST['selected_payment_method'];
    header("Location: create_checkout_session.php?method=$payment_method&address_id=" . $_SESSION['address_id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2 {
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .checkout-container {
            width: 90%;
            margin: 0 auto;
            margin-top: 30px;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        .checkout-btn {
            background-color: #111;
            color: #fff;
            padding: 14px 30px;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background-color: gray;
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: 20px;
            }

            .checkout-btn {
                padding: 12px 25px;
            }
        }
    </style>
</head>

<body>
    <div class="checkout-container" style="display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-start;">
        <!-- LEFT -->
        <div style="flex: 3; min-width: 320px;">
            <h2>Delivery Information</h2>
            <form id="checkoutForm" method="POST">
                <?php if (empty($addresses)): ?>
                    <p>No address found.</p>
                <?php else: ?>
                    <?php foreach ($addresses as $index => $address): ?>
                        <div style="border: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 15px; margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <label style="display: flex; align-items: flex-start; gap: 10px; flex: 1;">
                                    <input type="radio" name="address_id" value="<?php echo $address['id']; ?>" <?php if ($index === 0) echo 'checked'; ?> onclick="updateAddressId(<?php echo $address['id']; ?>)">
                                    <div>
                                        <strong><?php echo $address['address_line1']; ?>, <?php echo $address['address_line2']; ?></strong><br>
                                        <?php echo $address['postal_code']; ?> <?php echo $address['city']; ?>, <?php echo $address['country']; ?>
                                    </div>
                                </label>
                                <a href="edit_address.php?id=<?php echo $address['id']; ?>" style="color: gray; font-size: 14px; text-decoration: underline;">Update</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <a href="add_address.php" class="checkout-btn" style="margin-top: 10px; margin-bottom: 30px;">+ Add Address</a>

                <h2>Payment Method</h2>
                <div style="border: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                    <input type="radio" name="payment_method" value="cod" checked onclick="setPaymentMethod('cod')"> Cash on Delivery<br>
                    <input type="radio" name="payment_method" value="online" onclick="setPaymentMethod('online')"> Online Banking<br>
                    <input type="radio" name="payment_method" value="card" onclick="setPaymentMethod('card')"> Credit/Debit Card
                </div>

                <input type="hidden" name="selected_payment_method" id="selected_payment_method" value="cod">

                <!-- ✅ This sets the default selected address ID into the form -->
                <input type="hidden" name="address_id" id="address_id" value="<?php echo !empty($addresses) ? $addresses[0]['id'] : ''; ?>">

                <?php if (!empty($addresses)): ?>
                    <a type="submit" class="checkout-btn">Confirm Order</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- RIGHT: Order Summary -->
        <div style="flex: 1; min-width: 280px;">
            <div style="position: sticky; top: 20px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-height: 80vh; overflow-y: auto;">
                <h2>Order Summary</h2>
                <?php foreach ($cart_items as $item): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; gap: 10px;">
                        <img src="/products/<?php echo $item['image']; ?>" style="width: 60px; border-radius: 8px;" alt="Product">
                        <div style="flex: 1;">
                            <div style="font-weight: 600;"><?php echo $item['name']; ?></div>
                            <div style="font-size: 14px; color: #666;">Size: <?php echo strtoupper($item['size_label']); ?></div>
                            <div style="font-size: 14px;">RM<?php echo number_format($item['price'], 2); ?></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: bold;">x <?php echo $item['quantity']; ?></div>
                            <div style="font-size: 14px; font-weight: 600;">RM<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <hr style="margin: 20px 0;">
                <div style="font-size: 18px; font-weight: bold;">Total: RM<?php echo number_format($total_price, 2); ?></div>
            </div>
        </div>
    </div>

    <script>
        function setPaymentMethod(method) {
            document.getElementById('selected_payment_method').value = method;
        }

        function updateAddressId(addressId) {
            document.getElementById('address_id').value = addressId;
        }
    </script>
</body>

</html>

<?php include '_foot.php'; ?>