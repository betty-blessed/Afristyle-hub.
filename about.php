<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | AfriStyle Hub</title>
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

        .about-section {
            padding: 50px 20px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #6a0572;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #50025d;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="animated-title">About AfriStyle Hub</div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="about-section text-center p-4">
                <img src="images/logo.png" alt="AfriStyle Hub Logo" class="mb-3" width="150">
                <h2>Who We Are</h2>
                <p class="text-muted">

                    AfriStyle Hub is your go-to online store for best quality African wear. We specialize in well choosen,

                    beautifully designed, high-quality African attire that celebrates culture, tradition, and style.
                </p>

                <h3 class="mt-4">Our Mission</h3>
                <p class="text-muted">
                    Our mission is to bring Africa’s beauty to the world by offering stylish, custom-made outfits 
                    that reflect our rich heritage.
                </p>

                <h3 class="mt-4">Our Services</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-tshirt"></i> Custom-made African Attire</li>
                    <li class="list-group-item"><i class="fas fa-shipping-fast"></i> Fast & Reliable Delivery</li>
                    <li class="list-group-item"><i class="fas fa-shopping-cart"></i> Easy & Secure Ordering</li>
                    <li class="list-group-item"><i class="fas fa-phone"></i> 24/7 Customer Support</li>
                </ul>

                <h3 class="mt-4">Contact Us</h3>
                <p><i class="fas fa-envelope"></i> Email: <b>afristyle@gmail.com</b></p>
                <p><i class="fas fa-phone"></i> Mobile: <b>0729173041</b></p>
                <p><i class="fas fa-map-marker-alt"></i> Location: <b>Nairobi, Moi Avenue, Maua Building, Opposite Top Notch Hotel</b></p>

                <div class="mt-4">
                    <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                    <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
