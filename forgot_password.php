<?php
include 'db.php';
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '_head.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $errors = [];

    // Check if email exists in the database using PDO
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Generate token using sha1(uniqid() . rand())
        $token = sha1(uniqid(rand(), true));  // Token generation
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));  // Token expiry time

        // Insert token into the database using PDO
        $stmt = $pdo->prepare("INSERT INTO tokens (user_id, token, token_type, expires_at) VALUES (:user_id, :token, 'reset', :expires_at)");
        $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expires_at', $expires_at, PDO::PARAM_STR);
        $stmt->execute();

        // Prepare the password reset link
        $host = $_SERVER['HTTP_HOST'];
        $reset_link = "http://$host/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            // Server settings for SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'liaw.casual@gmail.com';
            $mail->Password   = 'buvq yftx klma vezl'; // App password
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
            $success = true;
        } catch (Exception $e) {
            $errors[] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $errors[] = "Email not found.";
    }
}
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

    .reset-container input[type="email"] {
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

    .reset-container .success-msg {
        color: green;
        text-align: center;
        margin-top: 15px;
        font-weight: 500;
    }

    .reset-container .error-msg {
        color: red;
        text-align: center;
        margin-top: 15px;
        font-weight: 500;
    }
</style>

<div class="reset-container">
    <h2>Forgot Password</h2>
    <form method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <?php
    if (isset($errors) && count($errors) > 0) {
        foreach ($errors as $err) {
            echo "<div class='error-msg'>$err</div>";
        }
    }

    if (isset($success) && $success) {
        echo "<div class='success-msg'>Reset link has been sent to your email.</div>";
    }
    ?>
</div>

<?php include '_foot.php'; ?>