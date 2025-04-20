<?php
include 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists and is not expired
    $stmt = $conn->prepare("SELECT user_id, expires_at FROM tokens WHERE token = ? AND token_type = 'reset'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Token exists, check expiration
        if (strtotime($row['expires_at']) > time()) {
            // Token is valid, show password reset form
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $new_password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update user's password
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $hashed_password, $row['user_id']);
                    $stmt->execute();

                    // Invalidate token
                    $stmt = $conn->prepare("DELETE FROM tokens WHERE token = ?");
                    $stmt->bind_param("s", $token);
                    $stmt->execute();

                    echo "Password has been successfully reset. <a href='login.php'>Login now</a>";
                } else {
                    echo "Passwords do not match.";
                }
            }
?>
            <form method="POST">
                <label>New Password:</label>
                <input type="password" name="password" required>
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>
                <button type="submit">Reset Password</button>
            </form>
<?php
        } else {
            echo "This reset link has expired. Please request a new one.";
        }
    } else {
        echo "Invalid reset link.";
    }
} else {
    echo "No token provided.";
}
?>