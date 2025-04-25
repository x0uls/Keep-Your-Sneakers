<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Payment Canceled</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            color: red;
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
    <h1>❌ Payment Canceled</h1>
    <p>Your payment was canceled. If you wish to complete the purchase, please try again.</p>
    <a href="checkout.php">Go Back to Checkout</a>
    <a href="index.php">Continue Shopping</a>
</body>

</html>