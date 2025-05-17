<?php
include 'includes/connection.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if user_id is set
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Prevent deletion of the logged-in admin
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account.";
        header("Location: manage_users.php");
        exit();
    }

    // Prevent deletion of other admins
    $check_admin = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
    $check_admin->bind_param("i", $user_id);
    $check_admin->execute();
    $result = $check_admin->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: manage_users.php");
        exit();
    }

    if ($user['user_type'] === 'admin') {
        $_SESSION['error'] = "Admin accounts cannot be deleted.";
        header("Location: manage_users.php");
        exit();
    }

    // Delete user
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $delete_stmt->bind_param("i", $user_id);

    if ($delete_stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting user: " . $conn->error;
    }

    header("Location: manage_users.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_users.php");
    exit();
}
?>
