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
        // Check token validity
        $stmt = $conn->prepare("SELECT * FROM tokens WHERE token = ? AND token_type = 'reset'");
        if (!$stmt) {
            echo "<div style='color: red;'>Error preparing statement: " . $conn->error . "</div>";
            exit;
        }
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($tokenData = $result->fetch_assoc()) {
            $expires_at = $tokenData['expires_at'];
            $current_time = date('Y-m-d H:i:s');

            if (strtotime($expires_at) > strtotime($current_time)) {
                // Token valid
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $tokenData['user_id']);
                if ($stmt->execute()) {
                    // Delete used token
                    $stmt = $conn->prepare("DELETE FROM tokens WHERE token = ?");
                    $stmt->bind_param("s", $token);
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
    }
} elseif (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']); // sanitize the token

    // Check if the token is valid in the database
    $stmt = $conn->prepare("SELECT * FROM tokens WHERE token = ? AND token_type = 'reset'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
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
} else {
    echo "<div style='color: red;'>No token found.</div>";
}

// Close prepared statements and connection when done (after all queries)
$stmt->close();
$conn->close();
