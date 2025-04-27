<?php
session_start();
require '../db.php'; // adjust if your db connection file is elsewhere
require '../lib/PHPMailer.php';
require '../lib/SMTP.php';
require '../lib/Exception.php';
require '../vendor/autoload.php'; // if using Composer, include this

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // if using Composer, include this

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Fetch the user's current password hash
    $stmt = $pdo->prepare('SELECT password, email FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || sha1($currentPassword) !== $user['password']) { // << changed
        $message = 'Current password is incorrect.';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'New passwords do not match.';
    } else {
        // Everything is fine — update password
        $newPasswordHash = sha1($newPassword); // << changed
        $updateStmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $updateStmt->execute([$newPasswordHash, $userId]);

        // Send email notification using PHPMailer
        $to = $user['email'];
        $subject = 'Your Password Has Been Changed';
        $body = 'Hello, your password has been successfully changed. If you did not make this change, please contact support immediately.';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'liaw.casual@gmail.com';
            $mail->Password   = 'buvq yftx klma vezl'; // App password (still original)
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('no-reply@yourdomain.com', 'Your Site Name');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($body);

            $mail->send();

            $message = 'Password successfully changed! Redirecting...';
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
        }

        .container {
            background: white;
            max-width: 500px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        input[type="password"] {
            width: 474px;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: black;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Change Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Current Password</label>
            <input type="password" name="current_password" required>

            <label>New Password</label>
            <input type="password" name="new_password" required>

            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Change Password</button>
        </form>
    </div>

    <?php if ($message == 'Password successfully changed! Redirecting...'): ?>
        <script>
            // Wait for 5 seconds and then redirect to the dashboard
            setTimeout(function() {
                window.location.replace('dashboard.php');
            }, 5000);
        </script>
    <?php endif; ?>

</body>

</html>