<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userMessage = trim($_POST['message']);

    // Simulate a basic AI response
    $responses = [
        "hello" => "Hi! Welcome to AfriStyle Hub. How can I assist you today?",
        "pricing" => "Our custom African wear starts from Ksh 1,500. Let us know what you're looking for!",
        "delivery" => "We offer delivery within 3-5 business days.",
        "bye" => "Goodbye! Have a great day!"
    ];

    $reply = "I'm not sure how to answer that. Can you rephrase?";
    foreach ($responses as $key => $response) {
        if (stripos($userMessage, $key) !== false) {
            $reply = $response;
            break;
        }
    }

    echo json_encode(["reply" => $reply]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfriStyle AI Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="padding: 10px;">
        <div id="chatbox" style="height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
            <p><strong>Bot:</strong> Hello! How can I assist you today?</p>
        </div>
        <input type="text" id="user-input" placeholder="Type a message..." style="width: 80%; padding: 5px;">
        <button id="send-btn">Send</button>
    </div>

    <script>
        $("#send-btn").click(function () {
            var userMessage = $("#user-input").val();
            $("#chatbox").append("<p><strong>You:</strong> " + userMessage + "</p>");
            $("#user-input").val("");

            $.post("chatbot.php", { message: userMessage }, function (data) {
                var response = JSON.parse(data);
                $("#chatbox").append("<p><strong>Bot:</strong> " + response.reply + "</p>");
                $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
            });
        });
    </script>
</body>
</html>
