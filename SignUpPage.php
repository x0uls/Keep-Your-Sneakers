<?php
include '_head.php';
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

        try {
            // Prepare and execute the PDO query
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Signup successful! You can now log in.";
                header("Location: LogInPage.php");
                exit();
            } else {
                $errors[] = "Error: Could not execute the query.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }

    $conn = null; // Close PDO connection
}
?>

<div class="signup-container">
    <h2>Sign Up</h2>
    <?php if (!empty($errors)) echo '<div class="error">' . implode('<br>', $errors) . '</div>'; ?>
    <form id="signup-form" action="" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" id="username" required><br>

        <label>Email:</label><br>
        <input type="email" name="email" id="email" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" id="password" required><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" id="confirm_password" required><br><br>

        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="LogInPage.php">Log in here</a>.</p>
</div>

<?php include '_foot.php'; // Include footer 
?>