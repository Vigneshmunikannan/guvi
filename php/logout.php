<?php
// Include Redis library
require 'vendor/autoload.php';

use Predis\Client;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$redis = new Client();

// Check if JWT token exists in request headers
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Authorization token is missing."));
    exit();
}

// Extract JWT token from authorization header
list(, $token) = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
// Decode JWT token
$key="Vignesh123@";
// Decode JWT token
try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array("message" => "Invalid token."));
    exit();
}


// Get username from decoded token
$username = $decoded->username;

// Delete user session from Redis
$redis->del($username);

// Return success response
http_response_code(200);
echo json_encode(array("message" => "Logout successful."));
?>
