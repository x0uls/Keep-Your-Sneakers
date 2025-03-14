<?php
$host = "localhost";
$user = "root"; // Default XAMPP user
$password = ""; // Leave empty for XAMPP
$database = "users_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
