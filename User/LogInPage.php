<?php
session_start();
include '_head.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $error = "No account associated with this email address.";
    } else {
        $stmt->bind_result($id, $hashed_password, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            if ($is_admin) {
                $_SESSION['admin'] = true;
                header("Location: admin_dashboard.php"); // Redirect admin users
            } else {
                header("Location: dashboard.php"); // Redirect regular users
            }
            exit();
        } else {
            $error = "Wrong password!";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<form method="POST">
    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email" required><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <a href="forgot_password.php">Forgot password?</a><br>

    <input type="submit" value="Login">

    <p>New User? <a href="SignUpPage.php">Sign Up here!</a></p><br><br>
</form>

<?php
// Display error message if there's an error
if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}

include '_foot.php';
?>