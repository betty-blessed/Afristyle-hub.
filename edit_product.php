<?php
include 'includes/connection.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product details
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: manage_products.php");
    exit();
}

// Fetch categories
$category_query = "SELECT * FROM categories";
$category_result = $conn->query($category_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category_id = $_POST['category_id'];
    $image = $product['image'];
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "images/";
        $image = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }
    
    // Update product details
    $update_query = "UPDATE products SET product_name=?, description=?, price=?, stock_quantity=?, category_id=?, image=? WHERE product_id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssdissi", $product_name, $description, $price, $stock_quantity, $category_id, $image, $product_id);
    if ($stmt->execute()) {
        header("Location: manage_products.php?success=Product updated successfully");
        exit();
    } else {
        $error = "Failed to update product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_nav.php'; ?>
    <div class="container mt-4">
        <h2>Edit Product</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="product_name" class="form-control" value="<?php echo $product['product_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required><?php echo $product['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock Quantity</label>
                <input type="number" name="stock_quantity" class="form-control" value="<?php echo $product['stock_quantity']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control" required>
                    <?php while ($row = $category_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['category_id']; ?>" <?php if ($row['category_id'] == $product['category_id']) echo 'selected'; ?>>
                            <?php echo $row['category_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Product Image</label>
                <input type="file" name="image" class="form-control">
                <img src="images/<?php echo $product['image']; ?>" class="mt-2" width="100">
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</body>
</html>