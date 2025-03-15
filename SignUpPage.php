<?php
include '_head.php'; // Include header
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];

    // Validate input
    if (empty($username)) $errors[] = "Username is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password) || strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Signup successful! You can now log in.";
            header("Location: SignInPage.php");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<div class="signup-container">
    <h2>Sign Up</h2>
    <?php if (!empty($errors)) echo '<div class="error">' . implode('<br>', $errors) . '</div>'; ?>
    <form id="signup-form" action="" method="POST">
        <label>Username:</label>
        <input type="text" name="username" id="username" required>

        <label>Email:</label>
        <input type="email" name="email" id="email" required>

        <label>Password:</label>
        <input type="password" name="password" id="password" required>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="SignInPage.php">Sign in here</a>.</p>
</div>

<?php include '_foot.php'; // Include footer 
?>