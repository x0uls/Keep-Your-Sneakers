<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: LogInPage.php");
    exit();
}

include 'db.php'; // Database connection

// Success and error messages
$message = '';
$message_type = '';

// Delete user logic with transaction
if (isset($_REQUEST['delete_id'])) {
    $delete_id = $_REQUEST['delete_id'];

    // Prevent deleting current admin
    if ($delete_id == $_SESSION['user_id']) {
        $message = "You cannot delete your own admin account!";
        $message_type = "error";
    } else {
        try {
            $pdo->beginTransaction();

            // First delete all related data (orders, etc.)
            // Example (uncomment if you have these tables):
            // $stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
            // $stmt->execute([$delete_id]);

            // Then delete the user
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$delete_id]);

            $pdo->commit();
            $message = "User deleted successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Error deleting user: " . $e->getMessage();
            $message_type = "error";
        }
    }

    // Refresh after delete to prevent form resubmission
    header("Location: user_manage.php?message=" . urlencode($message) . "&type=" . $message_type);
    exit();
}

// Search users logic
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        $searchTerm = '%' . $search . '%';
        $stmt = $pdo->prepare("SELECT id, username, email, is_admin FROM users 
                             WHERE username LIKE ? OR email LIKE ? 
                             ORDER BY username ASC");
        $stmt->execute([$searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->query("SELECT id, username, email, is_admin FROM users ORDER BY username ASC");
    }

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $message_type = "error";
}

// Check for message in URL parameters
if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
    $message_type = $_GET['type'] ?? 'info';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #111;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .admin-label {
            font-weight: bold;
            color: green;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .button-container a {
            padding: 10px 20px;
            background-color: #111;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .button-container a:hover {
            background-color: #333;
        }

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 10px;
            font-size: 16px;
            width: 40%;
            margin-right: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .search-container button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #111;
            color: white;
            border-radius: 6px;
            border: none;
        }

        .search-container button:hover {
            background-color: #333;
        }

        .delete-button {
            background: none;
            border: none;
            color: red;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            font-size: inherit;
            font-family: inherit;
        }

        .delete-button:hover {
            color: darkred;
            text-decoration: underline;
        }

        .floating-return {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: #111;
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .floating-return:hover {
            background: #333;
        }

        /* Message styles */
        .message {
            padding: 15px;
            margin: 20px auto;
            max-width: 80%;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .info {
            background-color: #d9edf7;
            color: #31708f;
            border: 1px solid #bce8f1;
        }

        form.inline-form {
            display: inline;
        }
    </style>
</head>

<body>

    <h2>User Management</h2>

    <!-- Display messages -->
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET" action="user_manage.php">
            <input type="text" name="search" placeholder="Search by username or email"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Users Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php echo $user['is_admin'] ? '<span class="admin-label">Admin</span>' : 'User'; ?>
                        </td>
                        <td>
                            <a href="edit_user.php?user_id=<?php echo $user['id']; ?>">Edit</a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form class="inline-form" action="user_manage.php" method="POST">
                                    <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="delete-button"
                                        onclick="return confirm('Are you sure you want to delete this user and all their data?');">
                                        Delete
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found matching your search criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Floating Back to Dashboard Button -->
    <a href="admin_dashboard.php" class="floating-return">← Back to Dashboard</a>

</body>

</html>