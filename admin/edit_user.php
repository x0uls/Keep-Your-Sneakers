<?php
session_start();
include '../db.php'; // adjust path if needed

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Initialize messages
$message = '';
$message_type = '';

// Handle update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?");
        $stmt->execute([$username, $email, $is_admin, $user_id]);

        $message = "User updated successfully.";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error updating user: " . $e->getMessage();
        $message_type = "error";
    }
}

// Fetch user data to edit
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('User not found.');
    }
} else {
    die('No user ID specified.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            padding: 40px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }

        form input[type="text"],
        form input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        form input[type="checkbox"] {
            margin-top: 15px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: #111;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #333;
        }

        .message {
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Edit User</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">

            <label for="username">Username</label>
            <input type="text" name="username" id="username" required value="<?php echo htmlspecialchars($user['username']); ?>">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($user['email']); ?>">

            <label>
                <input type="checkbox" name="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>> Admin
            </label>

            <button type="submit" class="submit-btn">Update User</button>
        </form>
    </div>

</body>

</html>