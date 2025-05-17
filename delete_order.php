<?php
include 'includes/connection.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Delete the order
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        header("Location: manage_orders.php");
        exit();
    } else {
        echo "Error deleting order.";
    }
}
?>
