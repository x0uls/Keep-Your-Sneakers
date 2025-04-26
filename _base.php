// emailNotification.php
<?php
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer's autoload is included

function sendEmail($userEmail)
{
    // Prepare the email content
    $subject = 'Your Password Has Been Changed';
    $body = 'Hello, your password has been successfully changed. If you did not make this change, please contact support immediately.';

    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.yourmailserver.com'; // Set the SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@yourdomain.com'; // Your SMTP username
        $mail->Password = 'your_email_password'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; // Or 465 for SSL

        //Recipients
        $mail->setFrom('no-reply@yourdomain.com', 'Your Site Name');
        $mail->addAddress($userEmail); // Add the user's email address

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body);

        // Send email
        $mail->send();
    } catch (Exception $e) {
        // Handle error (optional)
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>