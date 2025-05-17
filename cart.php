<?php
session_start();
include 'includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle cart item updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]); // Remove if quantity is 0
        }
    }
}

// Handle removing an item
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
}

// Calculate total amount
$total_amount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | AfriStyle Hub</title>
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
        <h2 class="text-center">Your Shopping Cart</h2>

        <?php if (empty($_SESSION['cart'])) { ?>
            <p class="text-center">Your cart is empty. <a href="shop.php">Go shopping</a></p>
        <?php } else { ?>
            <form method="POST" action="cart.php">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $product_id => $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image" width="50"></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control w-50 mx-auto">
                                </td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <a href="cart.php?remove=<?php echo $product_id; ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Remove
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between">
                    <h4>Total: $<?php echo number_format($total_amount, 2); ?></h4>
                    <button type="submit" name="update_cart" class="btn btn-info">Update Cart</button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        <?php } ?>
    </div>
</body>
</html>
