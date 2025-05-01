<?php
session_start();
require 'db.php';
require 'vendor/autoload.php';
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

\Stripe\Stripe::setApiKey('sk_test_51RHHzrQM571Me8gBwyQOUOteJKUfcw7aYlrKjTqFdRRoDbur4cj26Nv36rBizzKJqyvrWKitpdTv46Y6ntazcNmS00WE7tPID9'); // Replace with your secret key

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
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

    // Get the user's email from the session or database
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_email = $user['email'] ?? null;

    if (!$user_email) {
        die("No email found for user.");
    }

    // Get the address from the database using address_id from session
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
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, shipping_status, order_date, payment_id, address) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->execute([$user_id, $shipping_status, $payment_id, $address]);

        $order_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
    SELECT 
        c.product_id,
        c.size_id,
        c.quantity,
        p.price,
        p.name AS product_name,  -- Ensure you're fetching the product name
        p.image,  -- Fetch product image
        s.size_label  -- Fetch size label from sizes table
    FROM cart c
    JOIN products p ON c.product_id = p.id
    JOIN sizes s ON c.size_id = s.id  -- Ensure you're joining the sizes table
    WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart_items as $item) {
            // Calculate total price by multiplying the price by quantity
            $total_price = $item['price'] * $item['quantity'];

            // Insert into order_items with the calculated total price
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, size_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['size_id'],
                $item['quantity'],
                $total_price // Use the total price (price * quantity)
            ]);
        }

        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        $message = "Your payment has been processed successfully. Your order is now confirmed.";

        // Send the email notification
        sendOrderConfirmationEmail($user_email, $order_id, $cart_items, $message);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Error during order processing: ' . $e->getMessage());
        $message = "Something went wrong. Order was not processed.";
    }
} else {
    $message = "Payment was not successful. Please check your payment method and try again.";
}

function sendOrderConfirmationEmail($user_email, $order_id, $cart_items, $message)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'liaw.casual@gmail.com';
        $mail->Password   = 'buvq yftx klma vezl';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('noreply@KeepYourSneakers.com', 'Keep Your Sneakers');
        $mail->addAddress($user_email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - Order #$order_id";

        // Calculate total price
        $total_price = 0;
        foreach ($cart_items as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        // Start building HTML content
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .product { display: flex; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
                .product-details { flex: 1; }
                .product-name { font-weight: bold; }
                .product-price { text-align: right; font-weight: bold; }
                .total { font-size: 18px; font-weight: bold; margin-top: 20px; }
            </style>
        </head>
        <body>
            <h1>Thank you for your order!</h1>
            <p>' . $message . '</p>
            <p><strong>Order ID:</strong> ' . $order_id . '</p>
            <h2>Order Details</h2>';

        // Add cart items (no image)
        foreach ($cart_items as $item) {
            $html .= '
            <div class="product">
                <div class="product-details">
                    <div class="product-name">' . $item['quantity'] . ' × ' . $item['product_name'] . '</div>
                    <div>Size: ' . $item['size_label'] . '</div>
                    <div>Price: RM' . number_format($item['price'], 2) . ' each</div>
                </div>
                <div class="product-price">RM' . number_format($item['price'] * $item['quantity'], 2) . '</div>
            </div>';
        }

        $html .= '
            <div class="total">
                <hr>
                <div style="text-align: right;">
                    <strong>Total: RM' . number_format($total_price, 2) . '</strong>
                </div>
            </div>
        </body>
        </html>';

        $mail->Body = $html;
        $mail->send();
    } catch (Exception $e) {
        error_log('Error sending email: ' . $mail->ErrorInfo);
    }
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