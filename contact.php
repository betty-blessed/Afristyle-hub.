<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | AfriStyle Hub</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lobster&display=swap');

        body {
            background-color: #f8f9fa;
            background-image: url('https://www.transparenttextures.com/patterns/asfalt-dark.png'); /* Graffiti-style background */
        }

        .contact-header {
            font-family: 'Lobster', cursive;
            color: #ff4081;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        .contact-card {
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.2);
        }

        .contact-icon {
            font-size: 24px;
            color: #ff4081;
            transition: transform 0.3s;
        }

        .contact-icon:hover {
            transform: scale(1.2);
            color: #e60073;
        }

        /* Social Media Icons */
        .social-icons a {
            font-size: 30px;
            margin: 0 10px;
            color: #ff4081;
            transition: transform 0.3s, color 0.3s;
        }

        .social-icons a:hover {
            transform: rotate(10deg) scale(1.2);
            color: #e60073;
        }

        /* Floating Chat Button */
        .chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ff4081;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
            z-index: 1000;
        }

        .chat-button:hover {
            background-color: #e60073;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="contact-header">Get in Touch with AfriStyle Hub</h2>
        <p class="text-muted">We'd love to hear from you!</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card contact-card shadow-lg p-4">
                <div class="text-center mb-3">
                    <h4>Contact Information</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-phone contact-icon"></i> <strong>Mobile:</strong> 0729173041</li>
                    <li class="list-group-item"><i class="fas fa-envelope contact-icon"></i> <strong>Email:</strong> <a href="mailto:carlosbetty2015@gmail.com">afristyle@gmail.com</a></li>
                </ul>
                
                <!-- Social Media Links -->
                <div class="text-center mt-4 social-icons">
                    <a href="https://www.tiktok.com/@photon_ultrasound_xrays?is_from_webapp=1&sender_device=pc" target="_blank"><i class="fab fa-tiktok"></i></a>
                    <a href="https://x.com/junemaya123" target="_blank"><i class="fab fa-x-twitter"></i></a>
                    <a href="https://www.facebook.com/share/g/18C9BQBEmB/" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://wa.me/0720973441" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.instagram.com/photonmedicalservices/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Chat Button -->
<a href="chat.php" class="chat-button">
    Let's Chat
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
