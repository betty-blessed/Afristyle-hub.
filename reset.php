<?php
session_start();
include 'includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $new_password = isset($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        if ($new_password) {
            $update_sql = "UPDATE users SET password='$new_password' WHERE email='$email'";
            if ($conn->query($update_sql) === TRUE) {
                echo json_encode(["status" => "success", "message" => "Password changed successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error updating password."]);
            }
        } else {
            echo json_encode(["status" => "found", "message" => "Email found. Proceed to reset password."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email not found."]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lobster&display=swap');

        body {
            background-color: #f8f9fa;
        }

        .animated-title {
            font-family: 'Lobster', cursive;
            font-size: 2.5rem;
            color: #5A189A;
            text-align: center;
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        .card {
            border-radius: 15px;
        }

        .btn-reset {
            background: #6a0572;
            color: white;
            transition: 0.3s ease-in-out;
        }

        .btn-reset:hover {
            background: #50025d;
        }

        .icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a0572;
        }

        .input-group input {
            padding-left: 35px;
        }

        .links {
            text-align: center;
            margin-top: 10px;
        }

        .links a {
            text-decoration: none;
            font-weight: bold;
            color: #5A189A;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="animated-title">Reset Your Password</div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <div class="text-center mb-3">
                    <h2 style="font-family: 'Lobster', cursive; color: #5A189A;">Reset Password</h2>
                    <p class="text-muted">Enter your email to reset your password</p>
                </div>

                <form id="resetForm">
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>

                    <div id="passwordSection" class="mb-3 input-group" style="display:none;">
                        <span class="icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password">
                    </div>

                    <button type="submit" class="btn btn-reset w-100">
                        <i class="fas fa-paper-plane"></i> Submit
                        <span class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </form>

                <div id="message" class="mt-3 text-center"></div>

                <div class="links">
                    <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#resetForm").submit(function(event) {
        event.preventDefault();
        $(".loading-spinner").show(); // Show loading spinner
        $("#message").html("");

        let email = $("#email").val();
        let new_password = $("#new_password").val();
        let formData = { email: email };

        if ($("#passwordSection").is(":visible")) {
            formData.new_password = new_password;
        }

        $.ajax({
            type: "POST",
            url: "reset.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                $(".loading-spinner").hide();

                if (response.status === "found") {
                    $("#message").html('<div class="alert alert-info">Email found. Enter your new password.</div>');
                    $("#passwordSection").show();
                } else if (response.status === "success") {
                    $("#message").html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(() => { window.location.href = "login.php"; }, 2000);
                } else {
                    $("#message").html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
