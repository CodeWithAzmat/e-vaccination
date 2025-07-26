<?php
require 'db.php';

// Sample user data (you can customize or make it dynamic via a form)
$name = 'Test Parent';
$email = 'parent@gmail.com';
$password = '123456'; // plain password
$role = 'parent';

// Check if user already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = ?");
$check->bind_param("ss", $email, $role);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "User already exists with this email and role.";
    $check->close();
    exit;
}
$check->close();

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "✅ User created successfully.";
} else {
    echo "❌ Error creating user: " . $stmt->error;
}

$stmt->close();
$conn->close();
