<?php

header('Content-Type: application/json');
include '../includes/connection.php';


// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "mystore"; // Replace with your database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Retrieve and decode the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate the received data
if (!isset($data['payment_id']) || !isset($data['status']) || !isset($data['amount'])) {
    echo json_encode(["success" => false, "message" => "Invalid data received."]);
    exit;
}

// Sanitize inputs
$payment_id = $conn->real_escape_string($data['payment_id']);
$status = $conn->real_escape_string($data['status']);
$amount = $conn->real_escape_string($data['amount']);

// Insert payment data into the database
$sql = "INSERT INTO payments (payment_id, status, amount)  VALUES ('$payment_id', '$status', '$amount')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Payment recorded successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error saving payment: " . $conn->error]);
}

// Close the database connection
$conn->close();
?>
