<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: LogInPage.php");
    exit();
}

include 'db.php'; // PDO connection

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $file_name = $_FILES['profile_picture']['name'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allowed file extensions
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    // File size limit (5MB)
    if ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
        echo "File is too large. Maximum size is 5MB.";
        exit();
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_tmp);
    finfo_close($finfo);
    if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif'])) {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        exit();
    }

    if (in_array($file_ext, $allowed_extensions)) {
        $new_file_name = uniqid() . '.' . $file_ext;
        $upload_path = 'profilepic/' . $new_file_name;

        // Fetch the current profile picture from the database
        try {
            $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            $currentPic = $stmt->fetchColumn();

            // Delete old image if it's not the default
            if ($currentPic && $currentPic !== 'default-profile-icon.png') {
                $old_path = 'profilepic/' . $currentPic;
                if (file_exists($old_path)) {
                    unlink($old_path); // Delete the old file
                }
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            exit();
        }

        // Move the uploaded file to the destination folder
        if (move_uploaded_file($file_tmp, $upload_path)) {
            try {
                // Update the profile picture in the database
                $stmt = $pdo->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :id");
                $stmt->execute([
                    ':profile_picture' => $new_file_name,
                    ':id' => $_SESSION['user_id']
                ]);

                // Redirect with success message
                header("Location: dashboard.php?upload_success=true");
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
