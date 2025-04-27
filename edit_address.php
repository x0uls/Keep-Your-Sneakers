<?php
session_start();
require 'db.php';
include '_head.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Initialize success message
$success_message = '';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $address_id = $_GET['id'];

    // Fetch the address details based on the provided address_id
    $sql = "SELECT * FROM addresses WHERE user_id = :user_id AND id = :address_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_INT);
    $stmt->execute();

    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$address) {
        // Address not found or not owned by the user
        echo "Address not found.";
        exit;
    }

    // Handle form submission for updating the address
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $address_line1 = $_POST['address_line1'];
        $address_line2 = $_POST['address_line2'];
        $city = $_POST['city'];
        $postal_code = $_POST['postal_code'];
        $country = $_POST['country'];

        // Update the address in the database
        try {
            $updateSql = "UPDATE addresses SET 
                address_line1 = :address_line1,
                address_line2 = :address_line2,
                city = :city,
                postal_code = :postal_code,
                country = :country
                WHERE id = :address_id AND user_id = :user_id";

            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(':address_line1', $address_line1);
            $updateStmt->bindParam(':address_line2', $address_line2);
            $updateStmt->bindParam(':city', $city);
            $updateStmt->bindParam(':postal_code', $postal_code);
            $updateStmt->bindParam(':country', $country);
            $updateStmt->bindParam(':address_id', $address_id, PDO::PARAM_INT);
            $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $updateStmt->execute();

            // Set success message
            $success_message = "Address updated successfully.";
        } catch (PDOException $e) {
            echo "Error updating address: " . $e->getMessage();
        }
    }
} else {
    // If no address ID is provided, show an error or redirect
    echo "No address ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Address</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2 {
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .form-container {
            width: 60%;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        .form-container label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }

        .form-container input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .form-container button {
            background-color: #111;
            color: #fff;
            padding: 14px 30px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #333;
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
                width: 90%;
            }
        }
    </style>
    <script>
        // Redirect to checkout page after 5 seconds
        setTimeout(function() {
            window.location.href = 'checkout.php'; // Redirect URL
        }, 5000);
    </script>
</head>

<body>
    <div class="form-container">
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <h2>Edit Address</h2>
        <form method="POST">
            <label for="address_line1">Address Line 1:</label>
            <input type="text" name="address_line1" id="address_line1" value="<?php echo htmlspecialchars($address['address_line1']); ?>" required>

            <label for="address_line2">Address Line 2:</label>
            <input type="text" name="address_line2" id="address_line2" value="<?php echo htmlspecialchars($address['address_line2']); ?>">

            <label for="city">City:</label>
            <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($address['city']); ?>" required>

            <label for="postal_code">Postal Code:</label>
            <input type="text" name="postal_code" id="postal_code" value="<?php echo htmlspecialchars($address['postal_code']); ?>" required>

            <label for="country">Country:</label>
            <input type="text" name="country" id="country" value="<?php echo htmlspecialchars($address['country']); ?>" required>

            <button type="submit">Update Address</button>
        </form>
    </div>
</body>

</html>

<?php include '_foot.php'; ?>