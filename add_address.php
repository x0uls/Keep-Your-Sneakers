<?php
session_start();
require 'db.php';  // Ensure PDO connection is established at the beginning
include '_head.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data and save address
    $address_line1 = $_POST['address_line1'];
    $address_line2 = $_POST['address_line2'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];

    $sql = "INSERT INTO addresses (user_id, address_line1, address_line2, city, postal_code, country)
            VALUES (:user_id, :address_line1, :address_line2, :city, :postal_code, :country)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':address_line1', $address_line1, PDO::PARAM_STR);
    $stmt->bindParam(':address_line2', $address_line2, PDO::PARAM_STR);
    $stmt->bindParam(':city', $city, PDO::PARAM_STR);
    $stmt->bindParam(':postal_code', $postal_code, PDO::PARAM_STR);
    $stmt->bindParam(':country', $country, PDO::PARAM_STR);
    $stmt->execute();

    // Redirect to checkout after adding address
    header("Location: checkout.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Address</title>
    <style>
        /* Global Styles (Same as checkout) */
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

        .center {
            text-align: center;
        }

        .add-address-container {
            width: 90%;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #111;
        }

        .add-address-btn {
            background-color: #111;
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 20px;
        }

        .add-address-btn:hover {
            background-color: #333;
        }

        /* Footer */
        footer {
            background-color: #111;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            margin-top: 40px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .add-address-container {
                padding: 20px;
            }

            .form-group label,
            .form-group input {
                font-size: 14px;
            }

            .add-address-btn {
                padding: 12px 25px;
            }
        }
    </style>
</head>

<body>

    <div class="add-address-container">
        <h2>Add Shipping Address</h2>

        <form action="add_address.php" method="POST">
            <div class="form-group">
                <label for="address_line1">Address Line 1</label>
                <input type="text" name="address_line1" id="address_line1" required>
            </div>

            <div class="form-group">
                <label for="address_line2">Address Line 2</label>
                <input type="text" name="address_line2" id="address_line2">
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" name="city" id="city" required>
            </div>

            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" name="postal_code" id="postal_code" required>
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" name="country" id="country" required>
            </div>

            <button type="submit" class="add-address-btn">Save Address</button>
        </form>
    </div>

    <footer>
        &copy; 2025 Your Company. All rights reserved.
    </footer>

</body>

</html>

<?php include '_foot.php'; ?>