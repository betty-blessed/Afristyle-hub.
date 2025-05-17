<?php
session_start();
include 'includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];
            
            if ($user['user_type'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: customer_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .btn-login {
            background: #6a0572;
            color: white;
            transition: 0.3s ease-in-out;
        }

        .btn-login:hover {
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
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="running-text">Welcome Back to AfriStyle Hub!</div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <div class="text-center mb-3">
                    <h2 style="font-family: 'Lobster', cursive; color: #5A189A;">AfriStyle Hub</h2>
                    <p class="text-muted">Login to your account</p>
                </div>
                
                <?php if (isset($error)) { echo "<div class='alert alert-danger text-center'>$error</div>"; } ?>
                
                <form method="POST">
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="mb-3 input-group">
                        <span class="icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-login w-100 mb-2"><i class="fas fa-sign-in-alt"></i> Login</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="reset.php" class="text-decoration-none"><i class="fas fa-key"></i> Forgot Password?</a> |
                    <a href="register.php" class="text-decoration-none"><i class="fas fa-user-plus"></i> Create a New Account</a>
                </div>
                <div class="text-center mt-3">
                    <a href="contact.php" class="text-decoration-none"><i class="fas fa-envelope"></i> Contact Us</a> |
                    <a href="about.php" class="text-decoration-none"><i class="fas fa-info-circle"></i> About Us</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
