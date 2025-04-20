<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get cart items (if any)
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_items = count($cart_items);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneaker Store</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/app.css"> <!-- Link to external CSS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="js/cart.js"></script>
</head>

<body>

    <header>
        <div class="logo">
            <a href="index.php">
                <img src="images/store-logo.png" alt="Sneaker Store Logo">
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav>
            <a href="men.php" class="nav-btn">Men</a>
            <a href="women.php" class="nav-btn">Women</a>
            <a href="kids.php" class="nav-btn">Kids</a>
            <a href="sales.php" class="nav-btn">Sales</a>
        </nav>

        <!-- Right-side container for Sign-in/Profile, Search Bar & Cart -->
        <div class="right-header">

            <!-- Sign In Button / Profile Icon -->
            <div class="signin-container">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="profile-dropdown">
                        <a href="dashboard.php" class="profile-link">
                            <img src="images/profile-icon.png" alt="Profile" class="profile-icon">
                        </a>
                        <div class="dropdown-content">
                            <a href="dashboard.php">Profile</a>
                            <a href="logout.php" onclick="setTimeout(() => { location.reload(); }, 500);">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="LogInPage.php" class="signin-btn">Sign In</a>
                <?php endif; ?>
            </div>

            <!-- Search Bar -->
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Search for sneakers..." required>
                <button type="submit">
                    <img src="images/favicon.png" alt="Search" width="20" height="20">
                </button>
            </form>

            <!-- Cart Dropdown -->
            <div class="cart-dropdown">
                <button class="cart-btn">🛒 <span class="cart-count"><?php echo $total_items; ?></span></button>
                <div class="cart-content">
                    <?php if ($total_items > 0): ?>
                        <?php foreach ($cart_items as $id => $item): ?>
                            <div class="cart-item">
                                <img src="<?php echo $item['image']; ?>" alt="Product">
                                <div>
                                    <p><?php echo $item['name']; ?></p>
                                    <p>RM<?php echo $item['price']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="cart-footer">
                            <a href="cart.php">View Cart</a>
                        </div>
                    <?php else: ?>
                        <p>Your cart is empty</p>
                    <?php endif; ?>
                </div>
            </div>


        </div>

    </header>

</body>

</html>