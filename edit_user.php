<?php
include 'includes/connection.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get user ID from URL
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $query = "SELECT * FROM users WHERE user_id = $user_id";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $user_type = $_POST['user_type'];

    $update_query = "UPDATE users SET full_name=?, email=?, phone=?, address=?, user_type=? WHERE user_id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", $full_name, $email, $phone, $address, $user_type, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully!";
        header("Location: manage_users.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit User</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo $user['full_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $user['phone']; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"><?php echo $user['address']; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select name="user_type" class="form-control">
                    <option value="customer" <?php echo ($user['user_type'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                    <option value="admin" <?php echo ($user['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>