<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parent_id = $_SESSION['user_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    
    if (!$parent_id || !$name || !$phone || !$address) {
        echo "All fields are required.";
        exit;
    }

    // Handle image upload if provided
    $imageNameToStore = null;
    if (isset($_FILES['parent_image']) && $_FILES['parent_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['parent_image'];
        $fileName = basename($file['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . uniqid("parent_") . "_" . $fileName;

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Only JPG, PNG, and WEBP files are allowed.";
            exit;
        }

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $imageNameToStore = basename($targetFile);
        } else {
            echo "Failed to upload image.";
            exit;
        }
    }

    // If image was uploaded, include it in the update
    if ($imageNameToStore) {
        $stmt = $conn->prepare("UPDATE parents SET name = ?, phone = ?, address = ?, image = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $name, $phone, $address, $imageNameToStore, $parent_id);
    } else {
        $stmt = $conn->prepare("UPDATE parents SET name = ?, phone = ?, address = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $name, $phone, $address, $parent_id);
    }

    if ($stmt->execute()) {
        header("Location: parent.php?updated=1");
        exit;
    } else {
        echo "Failed to update: " . $stmt->error;
    }
}
?>
