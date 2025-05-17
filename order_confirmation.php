<?php
session_start();
include 'includes/connection.php';

// Check if an order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: shop.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'] ?? null;

// Ensure user is logged in
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Fetch order details
$order_query = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order_result = $order_query->get_result();
$order = $order_result->fetch_assoc();
$order_query->close();

// If no order found, redirect
if (!$order) {
    header("Location: shop.php");
    exit();
}

// Fetch order items
$item_query = $conn->prepare("SELECT oi.*, p.product_name, p.image FROM order_items oi 
                              JOIN products p ON oi.product_id = p.product_id 
                              WHERE oi.order_id = ?");
$item_query->bind_param("i", $order_id);
$item_query->execute();
$item_result = $item_query->get_result();
$item_query->close();

// Fetch payment status
$payment_query = $conn->prepare("SELECT * FROM payments WHERE order_id = ?");
$payment_query->bind_param("i", $order_id);
$payment_query->execute();
$payment_result = $payment_query->get_result();
$payment = $payment_result->fetch_assoc();
$payment_query->close();

// If payment record is missing, set default
$payment_status = $payment ? ucfirst($payment['payment_status']) : 'Not Found';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="customer_dashboard.php">AfriStyle Hub</a>
            <a href="shop.php" class="btn btn-primary me-2">Continue Shopping</a>
            <a href="customer_dashboard.php" class="btn btn-success">Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Order Confirmation</h2>

        <div class="alert alert-success text-center">
            <h4>Thank you for your order!</h4>
            <p>Your order ID is <strong>#<?php echo htmlspecialchars($order['order_id']); ?></strong></p>
            <p>Order Status: <strong><?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></strong></p>
            <p>Payment Status: <strong><?php echo htmlspecialchars($payment_status); ?></strong></p>
        </div>

        <h4>Order Summary</h4>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $item_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><img src="images/<?php echo htmlspecialchars($item['image']); ?>" width="50"></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></h4>

        <div class="text-center">
            <a href="shop.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
        </div>
    </div>
</body>
</html>
