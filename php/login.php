<?php
// Include JWT and Predis libraries
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Predis\Client;

// Function to verify user credentials with MySQL
function verifyCredentials($username, $password) {
    // Connect to MySQL database
    $mysqli = new mysqli("localhost", "root", "Vignesh123@", "user");

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Prepare SQL statement to verify credentials
    $stmt = $mysqli->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_username, $db_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $db_password)) {
            // Password is correct
            return true;
        }
    }

    // Close statement and connection
    $stmt->close();
    $mysqli->close();

    return false;
}

// Check if username and password are provided
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Username and password are required."));
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

// Verify user credentials
if (verifyCredentials($username, $password)) {
    // Generate JWT token
    $key = 'Vignesh123@';
    $payload = array(
        'username' => $username,
        'exp' => time() + (60 * 60) 
    );
    $token = JWT::encode($payload, $key,'HS256');

    // Store token in Redis
    $redis = new Client();
    $redis->setex($username, 3600, $token); // Set token with expiration time of 1 hour

    http_response_code(200);
    echo json_encode(array("message" => "Login successful", "token" => $token));
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Invalid username or password"));
}
?>
