<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['parent_image'])) {
    $parent_id = $_SESSION['user_id'] ?? null;

    if (!$parent_id) {
        echo "Not logged in.";
        exit;
    }

    $file = $_FILES['parent_image'];
    $fileName = basename($file['name']);
    $targetDir = "uploads/";
    $targetFile = $targetDir . uniqid("parent_") . "_" . $fileName;

    // Optional: validate file type/size
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo "Only JPG, PNG, and WEBP files are allowed.";
        exit;
    }

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        $fileNameToStore = basename($targetFile);

        // Update DB
        $stmt = $conn->prepare("UPDATE parents SET image = ? WHERE user_id = ?");
        $stmt->bind_param("si", $fileNameToStore, $parent_id);
        if ($stmt->execute()) {
            header("Location: parent.php"); // reload to show new image
            exit;
        } else {
            echo "Database update failed: " . $stmt->error;
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>
