<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: LogInPage.php"); // Redirect to login if not logged in
    exit();
}

include 'db.php';

// Check if a file has been uploaded
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $file_name = $_FILES['profile_picture']['name'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($file_ext, $allowed_extensions)) {
        // Create a unique file name
        $new_file_name = uniqid() . '.' . $file_ext;
        $upload_path = 'uploads/' . $new_file_name;

        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Update the user's profile picture in the database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $new_file_name, $_SESSION['user_id']);
            if ($stmt->execute()) {
                // Redirect back to the dashboard after updating the profile picture
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Failed to update the profile picture in the database.";
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
