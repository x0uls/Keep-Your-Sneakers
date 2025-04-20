<?php
include 'db.php';
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '_head.php';
include '_foot.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $errors = [];

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Generate token using sha1(uniqid() . rand())
        $token = sha1(uniqid(rand(), true));  // Token generation
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));  // Token expiry time (5 minutes from now)

        // Insert token into the database
        $stmt = $conn->prepare("INSERT INTO tokens (user_id, token, token_type, expires_at) VALUES (?, ?, 'reset', ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires_at);
        $stmt->execute();

        // Prepare the password reset link
        $host = $_SERVER['HTTP_HOST'];
        $reset_link = "http://$host/reset_password.php?token=" . $token;
        $mail = new PHPMailer(true);

        try {
            // Server settings for SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // Your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'liaw.casual@gmail.com'; // Your email address
            $mail->Password   = 'buvq yftx klma vezl'; // Your Gmail App password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipient settings
            $mail->setFrom('yourgmail@gmail.com', 'Your Website Name');
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "
                <p>Hi,</p>
                <p>You requested to reset your password. Click the link below to reset:</p>
                <a href='$reset_link'>$reset_link</a>
                <p>If you didn’t request this, you can safely ignore this email.</p>
            ";

            // Send the email
            $mail->send();
            echo "<div style='color: green;'>Reset link has been sent to your email.</div>";
        } catch (Exception $e) {
            echo "<div style='color: red;'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $errors[] = "Email not found.";
    }
}
?>

<form method="POST">
    <label>Email:</label>
    <input type="email" name="email" required>
    <button type="submit">Send Reset Link</button>
</form>