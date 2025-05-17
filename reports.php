<?php
include 'includes/connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['type'])) {
    $type = $_GET['type'];

    switch ($type) {
        case 'orders':
            $filename = "orders.csv";
            $query = "SELECT o.order_id, u.full_name AS customer, o.total_amount, o.payment_status, o.order_status, o.created_at
                      FROM orders o
                      JOIN users u ON o.user_id = u.user_id
                      ORDER BY o.created_at DESC";
            $columns = ['Order ID', 'Customer', 'Total Amount ($)', 'Payment Status', 'Order Status', 'Order Date'];
            break;

        case 'customers':
            $filename = "customers.csv";
            $query = "SELECT user_id, full_name, email, phone, address, user_type, created_at FROM users ORDER BY created_at DESC";
            $columns = ['User ID', 'Full Name', 'Email', 'Phone', 'Address', 'User Type', 'Created At'];
            break;

        case 'products':
            $filename = "products.csv";
            $query = "SELECT p.product_id, p.product_name, p.description, p.price, p.stock_quantity, c.category_name, p.created_at
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.category_id
                      ORDER BY p.created_at DESC";
            $columns = ['Product ID', 'Product Name', 'Description', 'Price ($)', 'Stock', 'Category', 'Created At'];
            break;

        case 'revenue':
            $filename = "revenue.csv";
            $query = "SELECT DATE(created_at) AS order_date, SUM(total_amount) AS total_revenue 
                      FROM orders 
                      WHERE payment_status = 'paid'
                      GROUP BY order_date
                      ORDER BY order_date DESC";
            $columns = ['Order Date', 'Total Revenue ($)'];
            break;

        default:
            die("Invalid export type.");
    }

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    // Set headers to trigger CSV file download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputcsv($output, $columns);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Records - AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php include 'admin_nav.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Download Records</h2>
    <p class="text-center">Select a record type to download the data as a CSV file.</p>

    <form action="" method="GET" class="text-center">
        <div class="mb-3">
            <select name="type" class="form-select w-50 mx-auto" required>
                <option value="">-- Select Record Type --</option>
                <option value="orders">Orders</option>
                <option value="customers">Customers</option>
                <option value="products">Products</option>
                <option value="revenue">Revenue</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Download</button>
    </form>
</div>

</body>
</html>
