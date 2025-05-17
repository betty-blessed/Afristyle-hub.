<?php
include 'includes/connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch admin details
$admin_query = "SELECT user_id FROM users WHERE user_type = 'admin' LIMIT 1";
$admin_result = $conn->query($admin_query);
if ($admin_result->num_rows > 0) {
    $admin = $admin_result->fetch_assoc();
    $admin_id = $admin['user_id'];
} else {
    die("No admin available.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Chat - AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <h3 class="text-center">Chat with Admin</h3>

    <div class="chat-box border p-3 bg-light" id="chat-box" style="height: 400px; overflow-y: auto;"></div>

    <form id="chat-form">
        <div class="input-group">
            <input type="text" id="message" class="form-control" placeholder="Type your message...">
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    function loadMessages() {
        $.get("fetch_messages.php", { user_id: <?= $user_id; ?>, admin_id: <?= $admin_id; ?> }, function(data) {
            $("#chat-box").html(data);
            $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
        });
    }

    $("#chat-form").on("submit", function(e) {
        e.preventDefault();
        let message = $("#message").val().trim();
        if (message === "") return;

        $.post("send_message.php", {
            recipient_id: <?= $admin_id; ?>,
            message: message
        }, function(response) {
            let res = JSON.parse(response);
            if (res.status === "success") {
                $("#message").val("");
                loadMessages();
            } else {
                alert(res.message);
            }
        });
    });

    setInterval(loadMessages, 3000);
    loadMessages();
});
</script>

</body>
</html>
