<?php
require 'vendor/autoload.php';
require 'db.php';
session_start();

\Stripe\Stripe::setApiKey('sk_test_51RHHzrQM571Me8gBwyQOUOteJKUfcw7aYlrKjTqFdRRoDbur4cj26Nv36rBizzKJqyvrWKitpdTv46Y6ntazcNmS00WE7tPID9'); // Replace with your actual Stripe Secret Key

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: LogInPage.php");
    exit;
}

// Get payment method from query string
$method = $_GET['method'] ?? 'card';
$method = ($method === 'online') ? 'fpx' : 'card';

$total_amount = 0;
$cart_items = [];

// Get user's cart items
$sql = "SELECT p.name, p.price, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build line items for Stripe
$line_items = [];
foreach ($cart_items as $item) {
    $line_items[] = [
        'price_data' => [
            'currency' => 'myr',
            'product_data' => ['name' => $item['name']],
            'unit_amount' => intval($item['price'] * 100), // Convert RM to sen
        ],
        'quantity' => $item['quantity'],
    ];
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$domain = $protocol . '://' . $_SERVER['HTTP_HOST'];  // Fixing the domain structure

// Ensure the URLs are fully correct and accessible. Replace this with your live domain if needed.
$success_url = $domain . '/paysuccess.php?session_id={CHECKOUT_SESSION_ID}';
$cancel_url = $domain . '/cart.php?cancelled=true';

// Debug: Print the URLs to ensure they are correct.
echo "Success URL: " . $success_url . "<br>";
echo "Cancel URL: " . $cancel_url . "<br>";

if (filter_var($success_url, FILTER_VALIDATE_URL) === false || filter_var($cancel_url, FILTER_VALIDATE_URL) === false) {
    die("Error: Invalid URL format.");
}

// Create the Stripe Checkout session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => [$method],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => $success_url,  // Correct success URL
    'cancel_url' => $cancel_url,  // Correct cancel URL
]);

// Redirect to Stripe Checkout
header("Location: " . $session->url);
exit;

// Redirect to Stripe Checkout
header("Location: " . $session->url);
exit;
