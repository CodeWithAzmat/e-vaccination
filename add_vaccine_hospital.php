<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hospital') {
    echo "Access denied.";
    exit;
}

$hospital_id = $_SESSION['user_id'];

$vaccine_name = trim($_POST['vaccine_name'] ?? '');
$disease_prevented = trim($_POST['disease_prevented'] ?? '');
$recommended_age = (int) ($_POST['recommended_age'] ?? 0);
$quantity = (int) ($_POST['quantity'] ?? 0);

if (
    empty($vaccine_name) ||
    empty($disease_prevented) ||
    $recommended_age <= 0 ||
    $quantity <= 0
) {
    echo "Please fill in all fields with valid values.";
    exit;
}

// First check if vaccine already exists for this hospital
$sql = "SELECT id, quantity FROM vaccines WHERE vaccine_name = ? AND hospital_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $vaccine_name, $hospital_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Vaccine exists, update quantity
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;

    $update = $conn->prepare("UPDATE vaccines SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $new_quantity, $row['id']);
    $update->execute();

} else {
    // Vaccine not found, insert new
    $insert = $conn->prepare("INSERT INTO vaccines (vaccine_name, disease_prevented, recommended_age, quantity, hospital_id) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("ssiii", $vaccine_name, $disease_prevented, $recommended_age, $quantity, $hospital_id);
    $insert->execute();
}

header("Location: hospital.php?vaccine_update=success");
exit;
?>
