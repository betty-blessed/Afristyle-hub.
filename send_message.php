<?php
include 'includes/connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$recipient_id = intval($_POST['recipient_id']);
$message = trim($_POST['message']);

// Validate input
if ($message == "") {
    echo json_encode(["status" => "error", "message" => "Message cannot be empty."]);
    exit();
}

// Determine sender (customer or admin)
$sender = ($user_type === 'customer') ? 'customer' : 'admin';

// Find correct user-admin pair
if ($sender === 'customer') {
    $query = "INSERT INTO conversations (user_id, admin_id, message, sender) VALUES (?, ?, ?, ?)";
} else {
    $query = "INSERT INTO conversations (user_id, admin_id, message, sender) VALUES (?, ?, ?, ?)";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("iiss", $user_id, $recipient_id, $message, $sender);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to send message."]);
}
?>
