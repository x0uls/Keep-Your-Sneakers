<?php
session_start();
require 'db.php';
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51RHHzrQM571Me8gBwyQOUOteJKUfcw7aYlrKjTqFdRRoDbur4cj26Nv36rBizzKJqyvrWKitpdTv46Y6ntazcNmS00WE7tPID9'); // Replace with your secret key

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: LogInPage.php");
    exit;
}

$session_id = $_GET['session_id'] ?? null;
if (!$session_id) {
    die("Invalid request. No session ID provided.");
}

try {
    $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
    $payment_intent = \Stripe\PaymentIntent::retrieve($checkout_session->payment_intent);
} catch (Exception $e) {
    error_log('Error retrieving session or payment intent: ' . $e->getMessage());
    die("Unable to verify payment. Please try again later.");
}

if ($payment_intent->status === 'succeeded') {
    $payment_id = $payment_intent->id;
    $shipping_status = 'Pending';

    // ✅ FIX: Get the address from the database using address_id from session
    $address_id = $_SESSION['address_id'] ?? null;
    if (!$address_id) {
        die("No address selected.");
    }

    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$address_id, $user_id]);
    $address_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$address_data) {
        die("Invalid address.");
    }

    // Format address into a single string
    $address = $address_data['address_line1'] . ', ' . $address_data['address_line2'] . ', ' .
        $address_data['postal_code'] . ' ' . $address_data['city'] . ', ' . $address_data['country'];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, shipping_status, created_at, payment_id, address) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->execute([$user_id, $shipping_status, $payment_id, $address]);

        $order_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, size_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['size_id'],
                $item['quantity'],
                $item['price'] // This assumes you're fetching price from the products table into the cart
            ]);
        }

        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        $message = "Your payment has been processed successfully. Your order is now confirmed.";
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Error during order processing: ' . $e->getMessage());
        $message = "Something went wrong. Order was not processed.";
    }
} else {
    $message = "Payment was not successful. Please check your payment method and try again.";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Success</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            color: green;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #111;
            color: #fff;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <h1>✅ Order Status</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="index.php">Continue Shopping</a>
</body>

</html>