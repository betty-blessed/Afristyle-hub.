<?php
session_start();
include 'includes/connection.php';

// Initialize variables
$order = null;
$error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];

    // Fetch order details
    $query = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $query->bind_param("ii", $order_id, $_SESSION['user_id']);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        $error = "Order not found or does not belong to you.";
    }
    $query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">AfriStyle Hub</a>
            <a href="customer_dashboard.php" class="btn btn-success">Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Track Your Order</h2>
        
        <form method="POST" class="text-center mt-3">
            <input type="text" name="order_id" class="form-control w-50 mx-auto" placeholder="Enter Order ID" required>
            <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-search"></i> Track Order</button>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-3 text-center"><?php echo $error; ?></div>
        <?php elseif ($order): ?>
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5>Order ID: #<?php echo $order['order_id']; ?></h5>
                    <p><strong>Status:</strong> <span class="badge bg-info"><?php echo ucfirst($order['order_status']); ?></span></p>
                    <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                    
                    <?php if ($order['order_status'] == 'shipped' || $order['order_status'] == 'delivered'): ?>
                        <p><strong>Expected Delivery:</strong> <?php echo date("F j, Y", strtotime($order['created_at'] . " +5 days")); ?></p>
                    <?php endif; ?>

                    <a href="customer_dashboard.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
