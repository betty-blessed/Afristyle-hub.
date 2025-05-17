<?php
include 'includes/connection.php';

if (!isset($_GET['order_id'])) {
    die("Order ID is missing.");
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$query = "SELECT o.order_id, o.user_id, u.full_name, u.email, u.phone, o.total_amount, o.payment_status, o.order_status, o.created_at
          FROM orders o
          JOIN users u ON o.user_id = u.user_id
          WHERE o.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

// Fetch ordered items
$query_items = "SELECT p.product_name, oi.quantity, oi.price 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($query_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php include 'admin_nav.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Order Details</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Order #<?php echo $order['order_id']; ?></h5>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Order Date:</strong> <?php echo $order['created_at']; ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
            
            <form method="POST" action="update_order.php">
                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                <label for="status">Order Status:</label>
                <select name="order_status" class="form-select" onchange="this.form.submit()">
                    <option value="processing" <?php if ($order['order_status'] == 'processing') echo 'selected'; ?>>Processing</option>
                    <option value="shipped" <?php if ($order['order_status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                    <option value="delivered" <?php if ($order['order_status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="cancelled" <?php if ($order['order_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
            </form>
        </div>
    </div>

    <h3 class="mt-4">Ordered Items</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (Ksh)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['price'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="manage_orders.php" class="btn btn-secondary">Back to Orders</a>
</div>

</body>
</html>
