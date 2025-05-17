<?php
include 'includes/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];

    // Check if order_id exists in the orders table
    $check_order_stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $check_order_stmt->bind_param("i", $order_id);
    $check_order_stmt->execute();
    $order_result = $check_order_stmt->get_result();

    if ($order_result->num_rows === 0) {
        echo "<script>alert('Error: Invalid Order ID.'); window.location.href='checkout.php';</script>";
        exit();
    }

    // Check if transaction ID is unique
    $check_transaction_stmt = $conn->prepare("SELECT * FROM payments WHERE transaction_id = ?");
    $check_transaction_stmt->bind_param("s", $transaction_id);
    $check_transaction_stmt->execute();
    $transaction_result = $check_transaction_stmt->get_result();

    if ($transaction_result->num_rows > 0) {
        echo "<script>alert('Error: Transaction ID already exists. Use a unique ID.'); window.location.href='checkout.php';</script>";
        exit();
    }

    // Insert payment record
    $insert_payment_stmt = $conn->prepare("INSERT INTO payments (order_id, payment_method, transaction_id, payment_status) VALUES (?, ?, ?, 'completed')");
    $insert_payment_stmt->bind_param("iss", $order_id, $payment_method, $transaction_id);

    if ($insert_payment_stmt->execute()) {
        // Update order status to paid
        $update_order_stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid' WHERE order_id = ?");
        $update_order_stmt->bind_param("i", $order_id);
        $update_order_stmt->execute();

        echo "<script>alert('Payment successful!'); window.location.href='customer_dashboard.php';</script>";
    } else {
        echo "<script>alert('Payment failed. Please try again.'); window.location.href='checkout.php';</script>";
    }
}
?>

<!-- Navigation Links -->
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="customer_dashboard.php">Dashboard</a></li>
        <li><a href="contact.php">Contact Us</a></li>
    </ul>
</nav>
