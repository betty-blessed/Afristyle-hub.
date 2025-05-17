<?php
include 'includes/connection.php';
include 'admin_nav.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No order selected!'); window.location.href='manage_orders.php';</script>";
    exit();
}

$order_id = $_GET['id'];

// Fetch order details
$query = "SELECT orders.*, users.full_name 
          FROM orders 
          JOIN users ON orders.user_id = users.user_id 
          WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Order not found!'); window.location.href='manage_orders.php';</script>";
    exit();
}

$order = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use correct column names: 'order_status' and 'payment_status'
    $order_status = $_POST['order_status'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';

    // Ensure values are valid
    $valid_order_statuses = ['processing', 'shipped', 'delivered', 'cancelled'];
    $valid_payment_statuses = ['pending', 'paid', 'failed'];

    if (!in_array($order_status, $valid_order_statuses) || !in_array($payment_status, $valid_payment_statuses)) {
        echo "<script>alert('Invalid status values!');</script>";
    } else {
        $update_query = "UPDATE orders SET order_status = ?, payment_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $order_status, $payment_status, $order_id);

        if ($stmt->execute()) {
            echo "<script>alert('Order updated successfully!'); window.location.href='manage_orders.php';</script>";
        } else {
            echo "Error updating order: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Update Order</h2>

    <form method="POST" class="p-4 shadow-lg bg-white">
        <div class="mb-3">
            <label class="form-label">Order ID</label>
            <input type="text" class="form-control" value="<?php echo $order['order_id']; ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Customer Name</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['full_name']); ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Total Amount ($)</label>
            <input type="text" class="form-control" value="<?php echo number_format($order['total_amount'], 2); ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Order Status</label>
            <select name="order_status" class="form-control" required>
                <option value="processing" <?php if ($order['order_status'] == 'processing') echo 'selected'; ?>>Processing</option>
                <option value="shipped" <?php if ($order['order_status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                <option value="delivered" <?php if ($order['order_status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                <option value="cancelled" <?php if ($order['order_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Payment Status</label>
            <select name="payment_status" class="form-control" required>
                <option value="pending" <?php if ($order['payment_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="paid" <?php if ($order['payment_status'] == 'paid') echo 'selected'; ?>>Paid</option>
                <option value="failed" <?php if ($order['payment_status'] == 'failed') echo 'selected'; ?>>Failed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Order</button>
    </form>
</div>

</body>
</html>
