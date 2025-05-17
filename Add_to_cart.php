<?php
session_start();
include 'includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if product_id is set
if (!isset($_POST['product_id']) || !isset($_POST['price'])) {
    header("Location: shop.php");
    exit();
}

$product_id = (int)$_POST['product_id'];
$price = (float)$_POST['price'];

// Fetch product details
$query = "SELECT product_name, stock_quantity, image FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Product not found!";
    header("Location: shop.php");
    exit();
}

$product = $result->fetch_assoc();

// Check stock availability
if ($product['stock_quantity'] <= 0) {
    $_SESSION['error'] = "Sorry, this product is out of stock.";
    header("Location: shop.php");
    exit();
}

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product is already in cart
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    $_SESSION['cart'][$product_id] = [
        'product_id' => $product_id,
        'product_name' => $product['product_name'],
        'image' => $product['image'],
        'price' => $price,
        'quantity' => 1
    ];
}

$_SESSION['success'] = "Product added to cart!";
header("Location: cart.php");
exit();
?>
