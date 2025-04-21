<?php
include 'db.php'; // Make sure this file uses PDO now

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Check if token exists and is not expired
        $stmt = $pdo->prepare("SELECT user_id, expires_at FROM tokens WHERE token = :token AND token_type = 'reset'");
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (strtotime($row['expires_at']) > time()) {
                // Token is valid, show password reset form
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $new_password = $_POST['password'];
                    $confirm_password = $_POST['confirm_password'];

                    if ($new_password === $confirm_password) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Update user's password
                        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                        $updateStmt->execute([
                            'password' => $hashed_password,
                            'id' => $row['user_id']
                        ]);

                        // Invalidate token
                        $deleteStmt = $pdo->prepare("DELETE FROM tokens WHERE token = :token");
                        $deleteStmt->execute(['token' => $token]);

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
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No token provided.";
}
?>