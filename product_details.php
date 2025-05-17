<?php
include 'includes/connection.php';
session_start();

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: shop.php");
    exit();
}
$product_id = intval($_GET['id']);

// Fetch product details
$query = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: shop.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            </div>
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                <p class="text-muted">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <h4 class="text-success">$<?php echo number_format($product['price'], 2); ?></h4>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>