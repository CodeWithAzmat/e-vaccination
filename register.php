<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // 🚫 Block admin registration
    if ($role === 'admin') {
        echo 'Admin registration is not allowed.';
        exit;
    }

    if (!$role || !$name || !$email || !$password) {
        echo 'Missing required fields';
        exit;
    }

    // Insert into users table
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

    if (!$stmt->execute()) {
        echo 'Error inserting user: ' . $stmt->error;
        exit;
    }

    $user_id = $stmt->insert_id;

    if ($role === 'parent') {
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';

        if (!$phone || !$address) {
            echo 'Missing parent fields';
            exit;
        }

        // ✅ Add name in parents table too
        $stmt2 = $conn->prepare("INSERT INTO parents (user_id, name, phone, address) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $user_id, $name, $phone, $address);

        if ($stmt2->execute()) {
            echo "Parent registered successfully";
        } else {
            echo "Error inserting parent: " . $stmt2->error;
        }
    }

    elseif ($role === 'hospital') {
        $location = $_POST['location'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $address = $_POST['address'] ?? '';

        if (!$location || !$contact || !$address) {
            echo 'Missing hospital fields';
            exit;
        }

        $stmt3 = $conn->prepare("INSERT INTO hospitals (hospital_name, location, contact_number, email, address, user_id, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt3->bind_param("sssssi", $name, $location, $contact, $email, $address, $user_id);

        if ($stmt3->execute()) {
            echo "Hospital registered successfully";
        } else {
            echo "Error inserting hospital: " . $stmt3->error;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'Invalid request method';
}
