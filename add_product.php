<?php
include 'includes/connection.php';
include 'admin_nav.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch categories for dropdown
$category_query = "SELECT * FROM categories ORDER BY category_name";
$category_result = $conn->query($category_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    $image = "default.jpg";
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "images/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // Insert product
    $insert_query = "INSERT INTO products (product_name, description, price, stock_quantity, image, category_id) 
                     VALUES ('$product_name', '$description', '$price', '$stock_quantity', '$image', '$category_id')";
    
    if ($conn->query($insert_query) === TRUE) {
        echo "<script>alert('Product added successfully!'); window.location.href='manage_products.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Add New Product</h2>
    <form method="POST" enctype="multipart/form-data" class="p-4 shadow-lg bg-white">
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
            <input type="number" class="form-control" name="price" step="0.01" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" name="stock_quantity" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-control" name="category_id" required>
                <option value="">Select Category</option>
                <?php while ($row = $category_result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['category_id']; ?>"> <?php echo $row['category_name']; ?> </option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Image</label>
            <input type="file" class="form-control" name="image">
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Product</button>
    </form>
</div>
</body>
</html>
