<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../index.php");
    exit();
}

include '../_head.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            padding: 60px 20px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .dashboard-title {
            font-size: 36px;
            margin-bottom: 40px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .dashboard-card {
            background-color: #fff;
            padding: 40px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-decoration: none;
            color: #111;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            background-color: #f9f9f9;
        }

        .dashboard-card h3 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .dashboard-card p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <h1 class="dashboard-title">Admin Dashboard</h1>

        <div class="card-grid">
            <a href="user_manage.php" class="dashboard-card">
                <h3>👤 User Management</h3>
                <p>Manage registered users, roles, and accounts.</p>
            </a>
            <a href="product_manage.php" class="dashboard-card">
                <h3>🛍️ Product Management</h3>
                <p>Add, update, or remove products and stock.</p>
            </a>
            <a href="shipping_manage.php" class="dashboard-card">
                <h3>🚚 Shipping Management</h3>
                <p>Track and update shipping details and couriers.</p>
            </a>
        </div>
    </div>

    <?php include '../_foot.php'; ?>
</body>

</html>