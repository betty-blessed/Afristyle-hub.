<?php
session_start();
include 'includes/connection.php';

// Fetch products from the database
$sql = "SELECT * FROM products ORDER BY id DESC LIMIT 6"; // Display latest 6 products
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfriStyle Hub | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero-section {
            background: url('images/hero-bg.jpg') no-repeat center center/cover;
            height: 300px;
            color: white;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-size: 1.5rem;
        }
        .product-card {
            border-radius: 10px;
            transition: 0.3s;
        }
        .product-card:hover {
            transform: scale(1.03);
        }
        .product-img {
            height: 200px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .btn-cart {
            background-color: #6a0572;
            color: white;
            transition: 0.3s;
        }
        .btn-cart:hover {
            background-color: #50025d;
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<div class="hero-section">
    <h1>Welcome to AfriStyle Hub</h1>
    <p>Shop the best African custom wear at affordable prices!</p>
</div>

<!-- Product Listing -->
<div class="container mt-5">
    <h2 class="text-center mb-4" style="color: #5A189A;">Featured Products</h2>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card shadow-sm">
                        <img src="uploads/<?php echo $row['image']; ?>" class="product-img card-img-top" alt="<?php echo $row['name']; ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="text-muted">Ksh <?php echo number_format($row['price'], 2); ?></p>
                            <button class="btn btn-cart add-to-cart" data-id="<?php echo $row['id']; ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p>No products available.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
        <a href="shop.php" class="btn btn-primary">View All Products</a>
    </div>
</div>

<script>
$(document).ready(function() {
    $(".add-to-cart").click(function() {
        var productId = $(this).data("id");

        $.ajax({
            type: "POST",
            url: "add_to_cart.php",
            data: { product_id: productId },
            success: function(response) {
                alert("Product added to cart!");
            },
            error: function() {
                alert("Error adding to cart.");
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
