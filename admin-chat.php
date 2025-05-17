<?php
include 'includes/connection.php';
session_start();

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch list of customers who have chatted
$customers_query = "SELECT DISTINCT user_id FROM conversations WHERE admin_id = ?";
$stmt = $conn->prepare($customers_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$customers_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat - AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'admin_nav.php'; ?>

<div class="container mt-4">
    <h3 class="text-center">Admin Chat</h3>

    <div class="row">
        <!-- Sidebar with customer list -->
        <div class="col-md-4">
            <h5>Customers</h5>
            <ul class="list-group">
                <?php while ($row = $customers_result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <a href="?customer_id=<?= $row['user_id']; ?>" class="text-decoration-none">
                            Customer #<?= $row['user_id']; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Chatbox -->
        <div class="col-md-8">
            <?php if (isset($_GET['customer_id'])): 
                $customer_id = intval($_GET['customer_id']);
            ?>
                <div class="chat-box border p-3 bg-light" id="chat-box" style="height: 400px; overflow-y: auto;"></div>

                <!-- Message Input -->
                <form id="chat-form">
                    <div class="input-group">
                        <input type="hidden" id="customer_id" value="<?= $customer_id; ?>">
                        <input type="text" id="message" class="form-control" placeholder="Type your message...">
                        <button class="btn btn-primary" type="submit">Send</button>
                    </div>
                </form>
            <?php else: ?>
                <p>Select a customer to start chatting.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function loadMessages() {
        let customer_id = $("#customer_id").val();
        if (!customer_id) return;

        $.get("fetch_messages.php", { user_id: customer_id, admin_id: <?= $admin_id; ?> }, function(data) {
            $("#chat-box").html(data);
            $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
        });
    }

    $("#chat-form").on("submit", function(e) {
        e.preventDefault();
        let message = $("#message").val().trim();
        if (message === "") return;

        $.post("send_message.php", {
            recipient_id: $("#customer_id").val(),
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
