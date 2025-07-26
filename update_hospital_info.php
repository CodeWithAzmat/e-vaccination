<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "Unauthorized access.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['hospital_name']);
    $address = trim($_POST['hospital_address']);
    $contact = trim($_POST['hospital_contact']);

    $stmt = $conn->prepare("UPDATE hospitals SET hospital_name = ?, address = ?, contact_number = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $name, $address, $contact, $user_id);

    if ($stmt->execute()) {
        header("Location: hospital.php?message=updated");
        exit;
    } else {
        echo "Error updating hospital info.";
    }

    $stmt->close();
}
?>
