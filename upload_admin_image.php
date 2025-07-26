<?php
session_start();
require 'db.php';

$admin_id = $_SESSION['user_id'] ?? null;

if ($admin_id && isset($_FILES['new_image'])) {
    $file = $_FILES['new_image'];
    $targetDir = "uploads/";
    $filename = basename($file["name"]);
    $targetFile = $targetDir . time() . "_" . $filename;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        $stmt = $conn->prepare("UPDATE users SET image = ? WHERE id = ?");
        $stmt->bind_param("si", $targetFile, $admin_id);
        $stmt->execute();
    }
}

header("Location: admin.php"); // Or wherever your admin dashboard is
exit;
