<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get cart items (if any)
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_items = count($cart_items);

// Fetch user profile picture if logged in
$profile_picture = 'default-profile-icon.png'; // Default profile picture
if (isset($_SESSION['user_id'])) {
    include 'db.php';
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($profile_picture);
    $stmt->fetch();
    $stmt->close();

    // If the user has a profile picture, use that
    if ($profile_picture && file_exists('uploads/' . $profile_picture)) {
        $profile_picture_path = 'uploads/' . $profile_picture;
    } else {
        $profile_picture_path = 'images/default-profile-icon.png'; // Fallback to default if no profile picture
    }
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
            <btn href="men.php" class="nav-btn">Men</btn>
            <btn href="women.php" class="nav-btn">Women</btn>
            <btn href="kids.php" class="nav-btn">Kids</btn>
            <btn href="sales.php" class="nav-btn">Sales</btn>
        </nav>

        <!-- Right-side container for Sign-in/Profile, Search Bar & Cart -->
        <div class="right-header">

            <!-- Sign In Button / Profile Icon -->
            <div class="signin-container">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="profile-dropdown">
                        <a href="dashboard.php" class="profile-link">
                            <img src="<?php echo $profile_picture_path; ?>" alt="Profile" class="profile-icon">
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
                    <div class="searchlogo">
                        <img src="images/search.png" alt="Search">
                    </div>
                </button>
            </form>

            <!-- Cart Button -->
            <div class="cart-button">
                <a href="cart.php">
                    <img src="images/cart.png" alt="Cart" style="width: 35px; height: 35px; position: fixed; top: 19px; right: 160px;" class="class-button" />
                </a>
            </div>

        </div>

    </header>

</body>

</html>