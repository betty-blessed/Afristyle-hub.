<?php
include 'includes/connection.php';

// Fetch orders along with customer details and payment status
$query = "SELECT o.order_id, o.user_id, u.full_name, o.total_amount, o.payment_status, o.order_status, o.created_at
          FROM orders o
          JOIN users u ON o.user_id = u.user_id
          ORDER BY o.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php include 'admin_nav.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Manage Orders</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Amount (KSH)</th>
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td>
                        <select name="order_status" class="form-select status-update" data-order-id="<?php echo $row['order_id']; ?>">
                            <option value="processing" <?php if ($row['order_status'] == 'processing') echo 'selected'; ?>>Processing</option>
                            <option value="shipped" <?php if ($row['order_status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="delivered" <?php if ($row['order_status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                            <option value="cancelled" <?php if ($row['order_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="view_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-info btn-sm">View</a>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['order_id']; ?>">Delete</button>
                    </td>
                </tr>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal<?php echo $row['order_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this order?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="delete_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $(".status-update").change(function () {
            var order_id = $(this).data("order-id");
            var new_status = $(this).val();
            
            $.ajax({
                url: "update_order.php",
                type: "POST",
                data: { order_id: order_id, order_status: new_status },
                success: function (response) {
                    alert("Order status updated successfully!");
                },
                error: function () {
                    alert("Error updating order status.");
                }
            });
        });
    });
</script>

</body>
</html>
