<?php
session_start();
include 'includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total_amount = 0;

// Calculate total amount
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Generate M-Pesa Password
function generateMpesaPassword()
{
    $shortcode = "174379"; 
    $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    $timestamp = date("YmdHis"); // Dynamic timestamp
    $password = base64_encode($shortcode . $passkey . $timestamp);

    return [
        "password" => $password,
        "timestamp" => $timestamp
    ];
}

// Generate M-Pesa Access Token
function generateAccessToken()
{
    $consumerKey = "7M6sYELmrlHFxIi1OfZTc55lZsV3AjmsYoc4W3sfgFFAzeOL";
    $consumerSecret = "VoAEMFINGDeFwHQ2z0g4jSylxbP2KEeJJiDVQMGGCRuG5xBdYuTcJl1OHy6w7jRQ";
    $credentials = base64_encode("$consumerKey:$consumerSecret");
    
    $ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    return $response['access_token'] ?? null;
}

// M-Pesa STK Push Request
function stkPushRequest($phone, $amount)
{
    $credentials = generateMpesaPassword();
    $password = $credentials['password'];
    $timestamp = $credentials['timestamp'];
    $businessShortCode = "174379";
    $partyB = "174379";
    $callbackUrl = "https://mydomain.com/path";
    
    $accessToken = generateAccessToken();
    if (!$accessToken) {
        return json_encode(["error" => "Failed to get access token"]);
    }

    $request_data = [
        "BusinessShortCode" => $businessShortCode,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => $partyB,
        "PhoneNumber" => $phone,
        "CallBackURL" => $callbackUrl,
        "AccountReference" => "AfriStyle Hub",
        "TransactionDesc" => "Payment for Order at AfriStyle Hub"
    ];

    $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

// Process Order & Payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $phone_number = trim($_POST['phone_number']);
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_status, order_status) VALUES (?, ?, 'pending', 'processing')");
    $stmt->bind_param("id", $user_id, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    $mpesaResponse = stkPushRequest($phone_number, $total_amount);
    unset($_SESSION['cart']);
    header("Location: order_confirmation.php?order_id=$order_id&mpesa_response=" . urlencode($mpesaResponse));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | AfriStyle Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="card p-4 shadow-lg">
        <h2 class="text-center">Checkout</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Enter M-Pesa Phone Number</label>
                <input type="text" name="phone_number" class="form-control" placeholder="07XXXXXXXX" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Amount</label>
                <input type="text" class="form-control" value="Ksh <?php echo number_format($total_amount, 2); ?>" disabled>
            </div>
            <button type="submit" name="place_order" class="btn btn-success w-100">Pay Now</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
