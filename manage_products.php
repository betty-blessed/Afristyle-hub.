<?php
include 'includes/connection.php';




// Handle search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $query = "SELECT * FROM products WHERE product_name LIKE ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $search_param = "%$search_query%";
    $stmt->bind_param("s", $search_param);
} else {
    $query = "SELECT * FROM products ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch categories for adding/editing products
$category_query = "SELECT * FROM categories";
$category_result = $conn->query($category_query);
$categories = $category_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include 'admin_nav.php'; ?>

    <div class="container mt-4">
        <h2 class="text-center">Manage Products</h2>

        <!-- Search Bar -->
        <form method="GET" class="d-flex mb-3">
            <input class="form-control me-2" type="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search products...">
            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Search</button>
        </form>

        <!-- Add Product Button -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus"></i> Add Product</button>

        <!-- Product Table -->
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price ($)</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><img src="images/<?php echo $row['image']; ?>" width="50"></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['category_id']; ?></td>
                        <td><?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['stock_quantity']; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                            <a href="view_product.php" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="category_id" required>
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Product</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
