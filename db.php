<?php
$host = "localhost";
$user = "root"; // replace if needed
$pass = "";     // replace with your DB password
$dbname = "e_vaccination";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    // If headers not sent, send JSON error (for API endpoints)
    if (!headers_sent()) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
        exit;
    } else {
        // Fallback for non-API usage
        echo "Database connection failed: " . $conn->connect_error;
        exit;
    }
}
