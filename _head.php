<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get cart items (if any)
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_items = count($cart_items);

// Fetch user profile picture if logged in
$profile_picture_path = '/profilepic/default-profile-icon.png'; // Default fallback\
if (isset($_SESSION['user_id'])) {
    include __DIR__ . '/db.php'; // Absolute path for safety
    try {
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $profile_picture = $stmt->fetchColumn();

        if ($profile_picture && file_exists(__DIR__ . '/../uploads/' . $profile_picture)) {
            $profile_picture_path = $_SERVER['DOCUMENT_ROOT'] . '/profilepic/' . $profile_picture;
        }
    } catch (PDOException $e) {
        // Just fallback to default
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sneaker Store</title>
    <link rel="icon" type="image/png" href="/images/favicon.png" />
    <link rel="stylesheet" href="/css/app.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>

    <header>
        <div class="logo">
            <a href="/index.php">
                <img src="/images/store-logo.png" alt="Sneaker Store Logo" />
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav>
            <a href="/page/men.php" class="nav-btn">Men</a>
            <a href="/page/women.php" class="nav-btn">Women</a>
            <a href="/page/kids.php" class="nav-btn">Kids</a>
            <a href="/page/sales.php" class="nav-btn">Sales</a>
        </nav>

        <!-- Right-side container -->
        <div class="right-header">

            <!-- Sign In / Profile Icon -->
            <div class="signin-container">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="profile-dropdown">
                        <a href="/dashboard.php" class="profile-link">
                            <img src="<?= $profile_picture_path ?>" alt="Profile" class="profile-icon" />
                        </a>
                        <div class="dropdown-content">
                            <a href="/dashboard.php">Profile</a>
                            <a href="/logout.php" onclick="setTimeout(() => { location.reload(); }, 500);">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/LogInPage.php" class="signin-btn">Sign In</a>
                <?php endif; ?>
            </div>

            <!-- Search Bar -->
            <form action="/search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Search for sneakers..." required />
                <button type="submit">
                    <div class="searchlogo">
                        <img src="/images/search.png" alt="Search" />
                    </div>
                </button>
            </form>

            <!-- Cart Button -->
            <a href="/cart.php">
                <img src="/images/cart.png" alt="Cart" class="cart-button" />
            </a>
        </div>
    </header>

</body>

</html>