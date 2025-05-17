<?php
function sendPushNotification($user_id, $message) {
    include 'includes/connection.php';

    // Insert notification into the database
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, status) VALUES (?, ?, 'unread')");
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
    $stmt->close();
}
?>
