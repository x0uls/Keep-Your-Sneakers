<?php
include '_head.php';
include 'db.php'; // PDO connection as $pdo

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username)) $errors[] = "Username is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password) || strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        // Hash the password with sha1
        $hashed_password = sha1($password);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Signup successful! You can now log in.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Could not create account. Try again.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<style>
    .signup-container {
        max-width: 400px;
        margin: 60px auto;
        padding: 40px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        font-family: 'Poppins', sans-serif;
    }

    .signup-container h2 {
        text-align: center;
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 30px;
    }

    .signup-container label {
        font-weight: 500;
        display: block;
        margin: 12px 0 6px;
    }

    .signup-container input[type="text"],
    .signup-container input[type="email"],
    .signup-container input[type="password"] {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #ccc;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .signup-container button {
        width: 100%;
        padding: 14px;
        background: #111;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .signup-container button:hover {
        background: #333;
    }

    .signup-container .error {
        background: #ffe5e5;
        color: red;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .signup-container p {
        text-align: center;
        margin-top: 15px;
    }

    .signup-container a {
        color: #111;
        font-weight: 500;
        text-decoration: underline;
    }
</style>

<div class="signup-container">
    <h2>Create Account</h2>

    <?php
    if (!empty($errors)) {
        echo '<div class="error">' . implode('<br>', $errors) . '</div>';
    }
    ?>

    <form id="signup-form" method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in here</a>.</p>
</div>

<?php include '_foot.php'; ?>