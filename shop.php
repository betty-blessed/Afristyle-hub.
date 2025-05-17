<?php 
include 'includes/connection.php';
session_start();

// Fetch categories for the filter
$category_query = "SELECT * FROM categories";
$category_result = $conn->query($category_query);

// Initialize filters
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 1000;
$category_id = $_GET['category_id'] ?? '';

// Build product query with filters
$query = "SELECT * FROM products WHERE price BETWEEN ? AND ?";
$params = [$min_price, $max_price];

if (!empty($category_id)) {
    $query .= " AND category_id = ?";
    $params[] = $category_id;
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('i', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfriStyle Hub | Shop</title>
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
                <li class="nav-item"><a class="nav-link active" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Filters -->
<div class="container mt-4">
    <h2 class="text-center">Shop Our Collection</h2>
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-md-3">
            <h5>Filter By:</h5>
            <form method="GET" action="shop.php">
                <label class="fw-bold">Price Range:</label>
                <input type="number" class="form-control mb-2" name="min_price" placeholder="Min Price" value="<?php echo htmlspecialchars($min_price); ?>">
                <input type="number" class="form-control mb-2" name="max_price" placeholder="Max Price" value="<?php echo htmlspecialchars($max_price); ?>">

                <label class="fw-bold">Category:</label>
                <select class="form-control mb-3" name="category_id">
                    <option value="">All Categories</option>
                    <?php while ($row = $category_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['category_id']; ?>" 
                            <?php echo ($row['category_id'] == $category_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </form>
        </div>

        <!-- Product Listings -->
        <div class="col-md-9">
            <div class="row">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()) { 
                        $image = !empty($row['image']) ? "images/" . htmlspecialchars($row['image']) : "images/default.jpg";
                    ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"> <?php echo htmlspecialchars($row['product_name']); ?> </h5>
                                    <p class="card-text"> <?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?> </p>
                                    <p class="card-text fw-bold">$<?php echo number_format($row['price'], 2); ?></p>
                                    <p class="card-text text-<?php echo $row['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $row['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                    </p>
                                    
                                    <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-info">View Details</a>

                                    <?php if ($row['stock_quantity'] > 0) { ?>
                                        <form method="POST" action="add_to_cart.php" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                            <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                                            <button type="submit" class="btn btn-success"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                                        </form>
                                    <?php } else { ?>
                                        <button class="btn btn-secondary" disabled>Out of Stock</button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No products found matching your filters.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
