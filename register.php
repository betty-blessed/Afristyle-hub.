<?php
include 'includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $role = $conn->real_escape_string($_POST['role']);

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists."]);
    } else {
        // Insert into database
        $sql = "INSERT INTO users (full_name, email, password, phone, address, user_type) VALUES ('$full_name', '$email', '$password', '$phone', '$address', '$role')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Registration successful! Redirecting..."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
        }
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lobster&display=swap');

        body {
            background-color: #f8f9fa;
        }

        .running-text {
            font-family: 'Lobster', cursive;
            font-size: 2rem;
            color: #5A189A;
            text-align: center;
            animation: moveText 5s linear infinite;
        }

        @keyframes moveText {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .card {
            border-radius: 15px;
        }

        .btn-register, .btn-login {
            transition: 0.3s ease-in-out;
        }

        .btn-register {
            background: #6a0572;
            color: white;
        }

        .btn-register:hover {
            background: #50025d;
        }

        .btn-login {
            background: #28a745;
            color: white;
        }

        .btn-login:hover {
            background: #1f7a33;
        }

        .icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a0572;
        }

        .input-group input, .input-group select {
            padding-left: 35px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="running-text">Welcome to AfriStyle Hub - Register Now!</div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <div class="text-center mb-3">
                    <h2 style="font-family: 'Lobster', cursive; color: #5A189A;">AfriStyle Hub</h2>
                    <p class="text-muted">Create your account</p>
                </div>

                <form id="registerForm">
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" required>
                    </div>
                    
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-phone"></i></span>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                    </div>
                    
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                        <textarea class="form-control" id="address" name="address" placeholder="Address"></textarea>
                    </div>

                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-user-tag"></i></span>
                        <select class="form-control" id="role" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-register w-100 mb-2"><i class="fas fa-user-plus"></i> Register</button>
                    <a href="login.php" class="btn btn-login w-100"><i class="fas fa-sign-in-alt"></i> Proceed to Login</a>
                </form>

                <div id="message" class="mt-3 text-center"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#registerForm").submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "register.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#message").html('<div class="alert alert-success">' + response.message + '</div>');
                    $("#registerForm")[0].reset();
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 2000);
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
