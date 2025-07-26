<?php
session_start();
require 'db.php';

$hospital_user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hospital_image']) && $hospital_user_id) {
    $uploadDir = 'uploads/';
    
    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = basename($_FILES['hospital_image']['name']);
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExtension, $allowedTypes)) {
        $newFileName = time() . '_' . $fileName;
        $targetFilePath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['hospital_image']['tmp_name'], $targetFilePath)) {
            // Save the image path to the database
            $stmt = $conn->prepare("UPDATE hospitals SET image = ? WHERE user_id = ?");
            $stmt->bind_param("si", $targetFilePath, $hospital_user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Redirect back to the hospital profile page
header('Location: hospital.php');
exit;
