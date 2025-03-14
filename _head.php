<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session only if it's not already active
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneaker Store</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/app.css"> <!-- Link to external CSS -->
</head>

<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="images/store-logo.png" alt="Sneaker Store Logo">
        </div>

        <!-- Sign In Button -->
        <div class="signin-container">
            <a href="SignInPage.php" class="signin-btn">Sign In</a>
        </div>
        </div>

        <!-- Navigation Menu -->
        <nav>
            <a href="men.php" class="nav-btn">Men</a>
            <a href="women.php" class="nav-btn">Women</a>
            <a href="kids.php" class="nav-btn">Kids</a>
            <a href="sales.php" class="nav-btn">Sales</a>
        </nav>

    </header>

</body>

</html>