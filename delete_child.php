<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id = isset($_POST['child_id']) ? intval($_POST['child_id']) : 0;
    if ($child_id) {
        $stmt = $conn->prepare("DELETE FROM children WHERE id=? AND parent_id=?");
        $stmt->bind_param("ii", $child_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            header('Location: parent.php');
            exit;
        } else {
            echo "<script>alert('Delete failed.');window.location='parent.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Missing child ID.');window.location='parent.php';</script>";
    }
    $conn->close();
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request."]);
