<?php
include 'db.php'; // Ensure connection is open
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '_head.php';
include '_foot.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['token'])) {
    $token = htmlspecialchars($_POST['token']); // sanitize the token
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<div style='color: red;'>Passwords do not match.</div>";
    } else {
        try {
            // Check token validity
            $stmt = $conn->prepare("SELECT * FROM tokens WHERE token = :token AND token_type = 'reset'");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tokenData) {
                $expires_at = $tokenData['expires_at'];
                $current_time = date('Y-m-d H:i:s');

                if (strtotime($expires_at) > strtotime($current_time)) {
                    // Token valid
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                    // Update password
                    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $tokenData['user_id'], PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        // Delete used token
                        $stmt = $conn->prepare("DELETE FROM tokens WHERE token = :token");
                        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                        $stmt->execute();

                        echo "<div style='color: green;'>Your password has been successfully reset. Redirecting to login...</div>";

                        // Redirect to login using PHP
                        header("Location: LogInPage.php");
                        exit; // Stop further script execution
                    } else {
                        echo "<div style='color: red;'>Failed to update password. Try again.</div>";
                    }
                } else {
                    echo "<div style='color: red;'>This token has expired. Please request a new password reset.</div>";
                }
            } else {
                echo "<div style='color: red;'>Invalid token.</div>";
            }
        } catch (PDOException $e) {
            echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
        }
    }
} elseif (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']); // sanitize the token

    try {
        // Check if the token is valid in the database
        $stmt = $conn->prepare("SELECT * FROM tokens WHERE token = :token AND token_type = 'reset'");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo '
                <form method="POST">
                    <input type="hidden" name="token" value="' . $token . '">
                    <label for="password">New Password:</label>
                    <input type="password" name="password" required>
                    <br>
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                    <br>
                    <button type="submit">Reset Password</button>
                </form>
            ';
        } else {
            echo "<div style='color: red;'>Invalid or expired token. Please request a new password reset.</div>";
        }
    } catch (PDOException $e) {
        echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div style='color: red;'>No token found.</div>";
}

// Close the connection
$conn = null;
