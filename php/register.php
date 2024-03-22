<?php
// Allow all CORS
// echo "Helloo";
require 'vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT");
header("Access-Control-Allow-Headers: Content-Type");
session_start();
$mysqli = new mysqli("localhost", "root", "Vignesh123@", "user");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
} else {
    echo "Connected successfully!";
}


try {
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $mongoDB = $mongoClient->selectDatabase('GUVI');
    $mongoCollection = $mongoDB->selectCollection('users');
    echo "mongo connected";
} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required_fields = ['name', 'username', 'lastname','age','dob','dept','location','domain', 'password'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo "All fields are required!";
            exit;
        }
    }
}
// Check if username already exists in MySQL
$existingUser = mysqli_prepare($mysqli, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($existingUser, "s", $_POST['username']);
mysqli_stmt_execute($existingUser);
mysqli_stmt_store_result($existingUser);

if(mysqli_stmt_num_rows($existingUser) > 0) {
    echo "Username already exists!";
    exit;
}

// Hash the password
$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Insert data into MySQL
$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $_POST['username'], $hashed_password);
if ($stmt->execute()) {
    // Insert data into MongoDB
    $userDetails = array(
        "username"=>$_POST['username'],
        "name" => $_POST['name'],
        "lastname" => $_POST['lastname'],
        "age" => $_POST['age'],
        "dob" => $_POST['dob'],
        "dept" => $_POST['dept'],
        "location" => $_POST['location'],
        "domain" => $_POST['domain']
    );
    $mongoCollection->insertOne($userDetails);

    echo "Registration successful!";
} else {
    echo "Error: " . $mysqli->error;
}

// Close statements and connections
$stmt->close();
$existingUser->close();
$mysqli->close();



?>
