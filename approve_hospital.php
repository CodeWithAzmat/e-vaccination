<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'approve' || $action === 'reject') {
        $status = $action;
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin.php"); // Redirect back
    exit;
}
?>
