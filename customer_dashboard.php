<?php
include 'includes/connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle product search
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query = "SELECT * FROM products WHERE product_name LIKE '%$search%' OR description LIKE '%$search%'";
} else {
    $query = "SELECT * FROM products";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfriStyle Hub | Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">AfriStyle Hub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Search Bar -->
<div class="container mt-3">
    <form class="d-flex" action="customer_dashboard.php" method="GET">
        <input class="form-control me-2" type="text" name="search" placeholder="Search for products..." value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-outline-primary" type="submit">Search</button>
    </form>
</div>

<!-- Products Section -->
<div class="container mt-4">
    <h2 class="text-center">Available Products</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="images/<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['product_name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"> <?php echo $row['product_name']; ?> </h5>
                            <p class="card-text"> <?php echo substr($row['description'], 0, 100) . '...'; ?> </p>
                            <p class="card-text fw-bold">$<?php echo number_format($row['price'], 2); ?></p>
                            <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-4">No products found.</div>
    <?php endif; ?>
</div>

</body>
</html>
