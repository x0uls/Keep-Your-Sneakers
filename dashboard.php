<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: LogInPage.php"); // Redirect to login if not logged in
    exit();
}

include '_head.php';
include 'db.php';

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($username, $email, $hashed_password);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<h2>Dashboard</h2>

<form>
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" value="********" readonly><br>

    <a href="logout.php">Logout</a>
</form>

<?php include '_foot.php'; ?>