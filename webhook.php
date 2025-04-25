<?php
require 'vendor/autoload.php';
require 'db.php';

\Stripe\Stripe::setApiKey('YOUR_SECRET_KEY'); // Replace with your Stripe secret key

// Retrieve the request's body and parse it as JSON
$input = @file_get_contents('php://input');
$event = null;

try {
    // Check if the webhook signature is valid
    $event = \Stripe\Webhook::constructEvent(
        $input,
        $_SERVER['HTTP_STRIPE_SIGNATURE'],
        'YOUR_STRIPE_ENDPOINT_SECRET' // Replace with your Stripe endpoint secret
    );
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

// Handle the event
switch ($event->type) {
    case 'invoice.payment_failed':
        // Payment failed, take appropriate action
        $invoice = $event->data->object; // Contains the invoice details
        // Here, you can store information or notify the user
        // For example, you can send an email or save the payment failure in your database

        // Redirect user to failure page
        header('Location: failure.php');
        exit;
        // Add more events as needed

    default:
        echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);
