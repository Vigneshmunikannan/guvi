<?php
// Include MongoDB and Redis libraries
require 'vendor/autoload.php';

use MongoDB\Client;
use Predis\Client as RedisClient;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // Connect to MongoDB
    $mongoClient = new Client("mongodb://localhost:27017");

    // Select MongoDB database and collection
    $mongoDB = $mongoClient->selectDatabase('GUVI');
    $mongoCollection = $mongoDB->selectCollection('users');

    // Connect to Redis
    $redis = new RedisClient();

    // Check if token is provided in the request headers
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        http_response_code(400);
        echo json_encode(array("message" => "Authorization token is missing."));
        exit();
    }

    // Extract token from authorization header
    list(, $token) = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);

    // Retrieve username associated with the token from Redis
    $key="Vignesh123@";
// Decode JWT token
try {
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array("message" => "Invalid token."));
    exit();
}

    // Check if username exists
    $username = $decoded->username;
    if (!$username) {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid token."));
        exit();
    }

    // Check if updated user data is provided
    if (!isset($_POST['updatedUserData'])) {
        http_response_code(400);
        echo json_encode(array("message" => "Updated user data is missing."));
        exit();
    }

    $updatedUserData = $_POST['updatedUserData'];

    // Update user profile in MongoDB
    $filter = ['username' => $username];
    $update = ['$set' => $updatedUserData];
    $result = $mongoCollection->updateOne($filter, $update);

    // Check if the update was successful
    if ($result->getModifiedCount() > 0) {
        // Profile updated successfully
        echo json_encode(array("message" => "Profile updated successfully."));
    } else {
        // Profile update failed
        http_response_code(500);
        echo json_encode(array("message" => "Failed to update profile."));
    }
} catch (\Exception $e) {
    // Handle any exceptions
    http_response_code(500);
    echo json_encode(array("message" => "An error occurred: " . $e->getMessage()));
}
?>
