<?php
session_start();
include 'includes/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure cart is not empty
if (empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_amount = 0;

// Calculate total price
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Process order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $payment_method = $_POST['payment_method'];

    // Insert order into the database
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_status, order_status) VALUES (?, ?, 'pending', 'processing')");
    $stmt->bind_param("id", $user_id, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get last inserted order ID
    $stmt->close();

    // Insert order items
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Insert payment record
    $transaction_id = uniqid("TXN_"); // Generate a unique transaction ID
    $stmt = $conn->prepare("INSERT INTO payments (order_id, payment_method, transaction_id, payment_status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iss", $order_id, $payment_method, $transaction_id);
    $stmt->execute();
    $stmt->close();

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to order confirmation
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">AfriStyle Hub</a>
            <a href="shop.php" class="btn btn-primary me-2">Shop</a>
            <a href="cart.php" class="btn btn-warning"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="customer_dashboard.php" class="btn btn-success">Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Checkout</h2>

        <div class="row">
            <div class="col-md-8">
                <h4>Order Summary</h4>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <h4>Total: $<?php echo number_format($total_amount, 2); ?></h4>
            </div>

            <div class="col-md-4">
                <h4>Payment Method</h4>
                <form method="POST" action="checkout.php">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="card" required>
                        <label class="form-check-label">Credit/Debit Card</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="mobile_money" required>
                        <label class="form-check-label">Mobile Money</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer" required>
                        <label class="form-check-label">Bank Transfer</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="cash_on_delivery" required>
                        <label class="form-check-label">Cash on Delivery</label>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-success w-100 mt-3">Place Order</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
