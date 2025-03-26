<?php
session_start();

echo "<h2>Session Test</h2>";

if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>You are <strong>logged in</strong> as User ID: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p style='color: red;'>You are <strong>logged out</strong></p>";
}
?>
<a href="logout.php">Logout</a> | <a href="LogInPage.php">Login</a>