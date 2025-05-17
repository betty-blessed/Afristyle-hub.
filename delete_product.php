<?php
session_start();
include 'includes/connection.php';

// ✅ Debug: Check if session variables are set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Session variables not set. Please log in again.");
}

// ✅ Ensure only admin can delete products
if ($_SESSION['role'] !== 'admin') {
    die("Access Denied! You are not an admin.");
}

// ✅ Validate product ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // ✅ Fix SQL query (use 'product_id' instead of 'id')
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete product.";
    }

    $stmt->close();
    $conn->close();

    // ✅ Redirect to products page
    header("Location: products.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: products.php");
    exit();
}
?>
