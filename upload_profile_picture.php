<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: LogInPage.php");
    exit();
}

include 'db.php'; // This file must now use PDO to create $pdo

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $file_name = $_FILES['profile_picture']['name'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($file_ext, $allowed_extensions)) {
        $new_file_name = uniqid() . '.' . $file_ext;
        $upload_path = 'uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :id");
                $stmt->execute([
                    ':profile_picture' => $new_file_name,
                    ':id' => $_SESSION['user_id']
                ]);

                header("Location: dashboard.php");
                exit();
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
            }
        } else {
            echo "Failed to upload the file.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    }
} else {
    echo "No file uploaded or there was an error with the file.";
}
