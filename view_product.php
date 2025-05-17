<?php
include 'includes/connection.php';
include 'admin_nav.php';



// Handle search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $query = "SELECT products.*, categories.category_name FROM products 
              LEFT JOIN categories ON products.category_id = categories.category_id
              WHERE product_name LIKE ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $search_param = "%$search_query%";
    $stmt->bind_param("s", $search_param);
} else {
    $query = "SELECT products.*, categories.category_name FROM products 
              LEFT JOIN categories ON products.category_id = categories.category_id 
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Product List</h2>

    <!-- Search Bar -->
    <form method="GET" class="d-flex mb-3">
        <input class="form-control me-2" type="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search products...">
        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Search</button>
    </form>

    <!-- Product Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price (Ksh)</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td>
                        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" width="50" height="50" class="rounded" 
                             onerror="this.onerror=null; this.src='images/default.jpg';">
                    </td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                        <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
