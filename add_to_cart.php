<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If item is already in cart, increase quantity
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'quantity' => 1
        ];
    }

    echo "Added to cart!";
}
