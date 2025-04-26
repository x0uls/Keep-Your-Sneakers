<?php
include 'db.php'; // PDO connection as $pdo
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '_head.php';
?>

<style>
    .reset-container {
        max-width: 400px;
        margin: 60px auto;
        padding: 40px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        font-family: 'Poppins', sans-serif;
    }

    .reset-container h2 {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 600;
        font-size: 28px;
    }

    .reset-container label {
        font-weight: 500;
        display: block;
        margin: 12px 0 6px;
    }

    .reset-container input[type="password"] {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #ccc;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .reset-container button {
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

    .reset-container button:hover {
        background: #333;
    }

    .reset-container .message {
        text-align: center;
        font-weight: 500;
        margin-top: 15px;
    }

    .reset-container .error-msg {
        color: red;
    }

    .reset-container .success-msg {
        color: green;
    }
</style>

<div class="reset-container">
    <h2>Reset Password</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['token'])) {
        $token = htmlspecialchars($_POST['token']);
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            echo "<div class='message error-msg'>Passwords do not match.</div>";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM tokens WHERE token = :token AND token_type = 'reset'");
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->execute();
                $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($tokenData) {
                    $expires_at = $tokenData['expires_at'];
                    $current_time = date('Y-m-d H:i:s');

                    if (strtotime($expires_at) > strtotime($current_time)) {
                        // Changed to SHA-1
                        $hashed_password = sha1($new_password);

                        // Update password
                        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                        $stmt->bindParam(':user_id', $tokenData['user_id'], PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            // Delete the token
                            $stmt = $pdo->prepare("DELETE FROM tokens WHERE token = :token");
                            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                            $stmt->execute();

                            echo "<div class='message success-msg'>Your password has been reset. Redirecting to login...</div>";
                            echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
                        } else {
                            echo "<div class='message error-msg'>Failed to update password. Try again.</div>";
                        }
                    } else {
                        echo "<div class='message error-msg'>This token has expired. Please request a new one.</div>";
                    }
                } else {
                    echo "<div class='message error-msg'>Invalid token.</div>";
                }
            } catch (PDOException $e) {
                echo "<div class='message error-msg'>Error: " . $e->getMessage() . "</div>";
            }
        }
    } elseif (isset($_GET['token'])) {
        $token = htmlspecialchars($_GET['token']);

        try {
            $stmt = $pdo->prepare("SELECT * FROM tokens WHERE token = :token AND token_type = 'reset'");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo '
                    <form method="POST">
                        <input type="hidden" name="token" value="' . $token . '">
                        <label for="password">New Password</label>
                        <input type="password" name="password" required>
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                        <button type="submit">Reset Password</button>
                    </form>
                ';
            } else {
                echo "<div class='message error-msg'>Invalid or expired token. Please request a new password reset.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='message error-msg'>Error: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='message error-msg'>No token found.</div>";
    }
    ?>
</div>

<?php include '_foot.php'; ?>