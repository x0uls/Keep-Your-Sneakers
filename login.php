<?php
session_start();
include '_head.php';
include 'db.php'; // Include your db connection

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Use the PDO connection
    $stmt = $pdo->prepare("SELECT id, password, is_admin FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() == 0) {
        $error = "No account associated with this email address.";
    } else {
        $user = $stmt->fetch();

        // Compare the hashed password using sha1
        if (sha1($password) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            if ($user['is_admin']) {
                $_SESSION['admin'] = true;
                header("Location: ../admin/admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Wrong password!";
        }
    }
}
?>

<style>
    .login-container {
        max-width: 400px;
        margin: 60px auto;
        padding: 40px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        font-family: 'Poppins', sans-serif;
    }

    .login-container h2 {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 600;
        font-size: 28px;
    }

    .login-container label {
        font-weight: 500;
        display: block;
        margin: 12px 0 6px;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #ccc;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .login-container a {
        font-size: 14px;
        color: #111;
        text-decoration: underline;
    }

    .login-container input[type="submit"] {
        width: 100%;
        padding: 14px;
        background: #111;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        margin-top: 20px;
        transition: background 0.3s ease;
    }

    .login-container input[type="submit"]:hover {
        background: #333;
    }

    .login-container p {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .login-container .error-msg {
        color: red;
        text-align: center;
        margin-top: 15px;
        font-weight: 500;
    }
</style>

<div class="login-container">
    <h2>Login</h2>
    <form method="POST">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <a href="../forgot_password.php">Forgot password?</a>

        <input type="submit" value="Login">

        <p>New User? <a href="signup.php">Sign up here</a></p>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
    </form>
</div>

<?php include '_foot.php'; ?>