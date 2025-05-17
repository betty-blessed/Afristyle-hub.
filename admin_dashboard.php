<?php
session_start();
include 'includes/connection.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch total counts
$total_orders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$total_products = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE user_type='customer'")->fetch_assoc()['total'];

// Fetch monthly revenue data
$revenue_data = [];
$month_query = $conn->query("
    SELECT SUM(total_amount) AS revenue, MONTH(created_at) AS month
    FROM orders WHERE payment_status = 'paid'
    GROUP BY MONTH(created_at)
");
while ($row = $month_query->fetch_assoc()) {
    $revenue_data[$row['month']] = $row['revenue'];
}

// Format revenue data for chart
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$chart_data = [];
for ($i = 1; $i <= 12; $i++) {
    $chart_data[] = $revenue_data[$i] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">AfriStyle Hub | Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php">Records</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Overview -->
    <div class="container mt-4">
        <h2 class="text-center mb-4">Admin Dashboard</h2>
        
        <div class="row text-center">
            <!-- Total Orders -->
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h3><?php echo $total_orders; ?></h3>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <h3><?php echo $total_products; ?></h3>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <h3><?php echo $total_customers; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="text-center">Monthly Revenue</h4>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart using Chart.js
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Revenue ($)',
                    data: <?php echo json_encode($chart_data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
