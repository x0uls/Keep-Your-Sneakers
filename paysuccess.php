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
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, shipping_status, created_at, payment_id, address) VALUES (?, ?, NOW(), ?, ?)");
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
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'liaw.casual@gmail.com';
        $mail->Password   = 'buvq yftx klma vezl'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('noreply@yourdomain.com', 'Your Store');
        $mail->addAddress($user_email); // User's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - Order #$order_id";

        // Calculate total price
        $total_price = 0;
        foreach ($cart_items as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        // Inline styles and HTML content
        $bodyContent = "<html>
                            <h1>Thank you for your order!</h1>
                            <p>$message</p>
                            <p><strong>Order ID:</strong> $order_id</p>
                            <p><strong>Items:</strong></p><ul>";

        // Add cart items to the email body
        foreach ($cart_items as $item) {
            // Generate a unique CID for each image
            $cid = md5($item['image']);

            // Embed the image into the email using the unique CID
            $mail->addEmbeddedImage($_SERVER['DOCUMENT_ROOT'] . '/products/' . $item['image'], $cid);

            // Add the product details with the embedded image in the email body
            $bodyContent .= "<li>
                                  <div>
                                      <img src='cid:$cid' alt='Product' style='width: 60px;'>
                                      <div>
                                          <div><strong>{$item['quantity']} x {$item['product_name']}</strong></div>
                                          <div>Size: {$item['size_label']}</div>
                                          <div>RM" . number_format($item['price'], 2) . "</div>
                                      </div>
                                  </div>
                                  <div style='text-align: right; font-weight: 600;'>RM" . number_format($item['price'] * $item['quantity'], 2) . "</div>
                              </li>";
        }



        $bodyContent .= "</ul><hr><p><strong>Total: RM" . number_format($total_price, 2) . "</strong></p></div></body></html>";

        $mail->Body = $bodyContent;

        // Send email
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