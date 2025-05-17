<?php
include 'includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['order_status']);

    $update_query = "UPDATE orders SET order_status='$new_status' WHERE order_id=$order_id";

    if ($conn->query($update_query)) {
        echo "Order status updated successfully.";
    } else {
        echo "Error updating order: " . $conn->error;
    }
}
?>
