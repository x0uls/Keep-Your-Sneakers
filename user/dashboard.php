<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../_head.php';
include '../db.php';
require '../lib/PHPMailer.php';
require '../lib/SMTP.php';
require '../lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch user details
$stmt = $pdo->prepare("SELECT username, email, profile_picture FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Handle password change request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_password'])) {
    $email = trim($_POST['email']);
    $errors = [];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = sha1(uniqid(rand(), true));
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $stmt = $pdo->prepare("INSERT INTO tokens (user_id, token, token_type, expires_at) VALUES (:user_id, :token, 'reset', :expires_at)");
        $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expires_at', $expires_at, PDO::PARAM_STR);
        $stmt->execute();

        $host = $_SERVER['HTTP_HOST'];
        $change_link = "http://$host/reset_password.php?token=" . $token;
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'liaw.casual@gmail.com';
            $mail->Password   = 'buvq yftx klma vezl';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('yourgmail@gmail.com', 'Your Website Name');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Change Your Password';
            $mail->Body    = "
                <p>Hi,</p>
                <p>You requested to change your password. Click the link below to change it:</p>
                <a href='$change_link'>$change_link</a>
                <p>If you didn’t request this, you can safely ignore this email.</p>
            ";

            $mail->send();
            $success_message = "Password change link has been sent to your email.";
        } catch (Exception $e) {
            $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error_message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        .dashboard-container-wrapper {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            padding: 60px 20px;
            display: flex;
            justify-content: center;
        }

        /* Main Dashboard Layout */
        .main-container {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 20px;
            width: 100%;
            max-width: 1100px;
        }

        /* Sidebar (Button Container) */
        .button-container {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 200px;
            display: flex;
            flex-direction: column;
        }

        .button-container a {
            display: block;
            padding: 12px;
            color: #333;
            text-decoration: none;
            text-align: center;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 12px;
            background-color: #fff;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .button-container a:hover {
            background-color: #ddd;
        }

        /* Main Content */
        .dashboard-container {
            background-color: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        .dashboard-container h2 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 24px;
            color: #111;
        }

        .dashboard-container img {
            border-radius: 50%;
            margin-bottom: 16px;
        }

        .dashboard-container label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: 500;
            color: #333;
        }

        .dashboard-container input[type="text"],
        .dashboard-container input[type="email"],
        .dashboard-container input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .dashboard-container button {
            background-color: #111;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .dashboard-container button:hover {
            background-color: #333;
        }

        .dashboard-container .logout-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #e63946;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .dashboard-container .logout-link:hover {
            color: #b71c1c;
        }

        .dashboard-container .message-success {
            color: green;
            margin-top: 15px;
        }

        .dashboard-container .message-error {
            color: red;
            margin-top: 15px;
        }
    </style>

</head>

<body>

    <div class="dashboard-container-wrapper">
        <div class="main-container">
            <!-- Sidebar (Button Container) -->
            <div class="button-container">
                <a href="dashboard.php">Dashboard</a>
                <a href="orders.php">Orders</a>
            </div>

            <!-- Main Content -->
            <div class="dashboard-container">
                <h2>Dashboard</h2>

                <?php
                $profile_picture_path = $user['profile_picture'] ? "../profilepic/" . htmlspecialchars($user['profile_picture']) : "../profilepic/default-profile-icon.png";
                ?>
                <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" width="150" height="150">

                <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
                    <label for="profile_picture">Change Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                    <button type="submit">Upload New Picture</button>
                </form>

                <form method="POST">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>

                    <button type="submit" name="change_password">Change Password</button>
                </form>

                <a href="../logout.php" class="logout-link">Logout</a>

                <?php if (isset($success_message)): ?>
                    <div class="message-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="message-error"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../_foot.php'; ?>
</body>

</html>