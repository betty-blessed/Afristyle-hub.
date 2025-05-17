<?php
// pesapal_callback.php

// Include necessary files and initialize database connection
include 'includes/connection.php';
include 'pesapal_oauth.php'; // Ensure you have the OAuth library provided by Pesapal

// Define your consumer key and secret
$consumer_key = 'YourConsumerKey';
$consumer_secret = 'YourConsumerSecret';

// Get the notification details from Pesapal
$pesapalNotification = $_GET['pesapal_notification_type'];
$pesapalTrackingId = $_GET['pesapal_transaction_tracking_id'];
$pesapalMerchantReference = $_GET['pesapal_merchant_reference'];

// Construct the status request URL
$statusRequestAPI = 'https://www.pesapal.com/api/querypaymentstatus';
$token = $params = null;

// Create OAuth consumer
$consumer = new OAuthConsumer($consumer_key, $consumer_secret);

// Create OAuth request
$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
$request_status = OAuthRequest::from_consumer_and_token(
    $consumer,
    $token,
    "GET",
    $statusRequestAPI,
    $params
);
$request_status->set_parameter("pesapal_merchant_reference", $pesapalMerchantReference);
$request_status->set_parameter("pesapal_transaction_tracking_id", $pesapalTrackingId);
$request_status->sign_request($signature_method, $consumer, $token);

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $request_status->to_url());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    // Log cURL error
    error_log('cURL error: ' . curl_error($ch));
    curl_close($ch);
    exit;
}

curl_close($ch);

// Parse the response
$responseData = explode("\r\n\r\n", $response);
$header = isset($responseData[0]) ? $responseData[0] : '';
$body = isset($responseData[1]) ? $responseData[1] : '';

// Check if the response contains valid XML
if (strpos($header, '200 OK') !== false && !empty($body)) {
    $xml = simplexml_load_string($body);

    if ($xml) {
        $status = $xml->payment_status;
        $paymentMethod = $xml->payment_method;
        $amount = $xml->amount;

        // Update your database based on the payment status
        $stmt = $conn->prepare("UPDATE orders SET payment_status = ?, payment_method = ?, amount_paid = ? WHERE order_reference = ?");
        $stmt->bind_param("ssds", $status, $paymentMethod, $amount, $pesapalMerchantReference);
        $stmt->execute();
        $stmt->close();

        // Respond to Pesapal to acknowledge receipt
        echo "pesapal_notification_type=$pesapalNotification&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapalMerchantReference";
    } else {
        // Log XML parsing error
        error_log('Error parsing XML response from Pesapal.');
    }
} else {
    // Log invalid response
    error_log('Invalid response from Pesapal.');
}
?>
