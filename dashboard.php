<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: LogInPage.php"); // Redirect to login if not logged in
    exit();
}

include '_head.php';
include 'db.php';  // Ensure database connection is established at the beginning
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch user details including profile picture
$stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($username, $email, $profile_picture);
$stmt->fetch();
$stmt->close();  // Close after fetching the details

// Handle the password change request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_password'])) {
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

        // Insert token into the database with token_type 'change'
        $stmt = $conn->prepare("INSERT INTO tokens (user_id, token, token_type, expires_at) VALUES (?, ?, 'reset', ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires_at);
        $stmt->execute();

        // Prepare the change password link
        $host = $_SERVER['HTTP_HOST'];
        $change_link = "http://$host/reset_password.php?token=" . $token; // Link for changing password
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
            $mail->Subject = 'Change Your Password';
            $mail->Body    = "
                <p>Hi,</p>
                <p>You requested to change your password. Click the link below to change it:</p>
                <a href='$change_link'>$change_link</a>
                <p>If you didn’t request this, you can safely ignore this email.</p>
            ";

            // Send the email
            $mail->send();
            echo "<div style='color: green;'>Password change link has been sent to your email.</div>";
        } catch (Exception $e) {
            echo "<div style='color: red;'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $errors[] = "Email not found.";
    }
}

// Don't close the connection yet, as you might need it again

?>

<h2>Dashboard</h2>

<!-- Display Profile Picture -->
<div style="text-align: center;">
    <?php
    // Check if there is a profile picture, if not, set default image
    $profile_picture_path = $profile_picture ? "uploads/" . htmlspecialchars($profile_picture) : "images/default-profile-icon.png";
    ?>
    <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" width="150" height="150" style="border-radius: 50%;"><br><br>

    <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
        <label for="profile_picture">Change Profile Picture:</label><br>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required><br><br>
        <button type="submit">Upload New Picture</button>
    </form>
</div>

<form method="POST">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly><br>

    <!-- Change Password Section -->
    <button type="submit" name="change_password">Change Password</button><br><br>

    <a href="logout.php">Logout</a>
</form>

<?php include '_foot.php'; ?>

<?php
// Close the connection after all queries have been executed
$conn->close();
?>