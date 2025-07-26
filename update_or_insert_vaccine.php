<?php
session_start();
require 'db.php';

$hospital_id = $_SESSION['user_id'] ?? null;

if (!$hospital_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: hospital_dashboard.php");
    exit;
}

$vaccine_name = trim($_POST['vaccine_name']);
$add_quantity = intval($_POST['add_quantity']);

// 1. Check if this vaccine already exists for this hospital
$sql = "SELECT * FROM vaccines WHERE hospital_id = ? AND vaccine_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $hospital_id, $vaccine_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ✅ Vaccine exists – update quantity
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $add_quantity;

    $update = $conn->prepare("UPDATE vaccines SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $new_quantity, $row['id']);
    $update->execute();
} else {
    // ❌ Vaccine not found – insert a new record
    $disease_prevented = $_POST['disease_prevented'] ?? 'Unknown';
    $recommended_age = $_POST['recommended_age'] ?? 'Any';

    $insert = $conn->prepare("INSERT INTO vaccines (vaccine_name, disease_prevented, recommended_age, quantity, hospital_id) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("sssii", $vaccine_name, $disease_prevented, $recommended_age, $add_quantity, $hospital_id);
    $insert->execute();
}

header("Location: hospital.php"); // Redirect back to dashboard
exit;
?>
