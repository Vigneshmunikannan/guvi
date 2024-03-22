<?php
// Include MongoDB and Redis libraries
require 'vendor/autoload.php';
use MongoDB\Client;
use Predis\Client as RedisClient;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Connect to MongoDB
$mongoClient = new Client("mongodb://localhost:27017");

// Select MongoDB database and collection
$mongoDB = $mongoClient->selectDatabase('GUVI');
$mongoCollection = $mongoDB->selectCollection('users');

// Connect to Redis
$redis = new RedisClient();

// Check if JWT token exists in request headers
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Authorization token is missing."));
    exit();
}

// Extract JWT token from authorization header
list(, $token) = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);

// Decode JWT token

$key = 'Vignesh123@'; // Replace with your actual secret key
try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    // Get username from decoded token
    $username = $decoded->username;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array("message" => "Invalid token."));
    exit();
}

// Fetch token from Redis using the username as key
$storedToken = $redis->get($username);

// Check if the stored token matches the token provided in the header
if ($storedToken !== $token) {
    http_response_code(401);
    echo json_encode(array("message" => "Invalid token."));
    exit();
}


// Fetch user data from MongoDB based on the username
$userData = $mongoCollection->findOne(['username' => $username]);

if ($userData) {
    echo json_encode($userData);
} else {
    // User data not found
    http_response_code(404);
    echo json_encode(array("message" => "User data not found.$username"));
}
?>
