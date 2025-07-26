<?php
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$appointment_id = $data['id'] ?? null;
$status = $data['status'] ?? null;

if (!$appointment_id || !$status) {
    echo json_encode(["success" => false, "message" => "Missing ID or status"]);
    exit;
}

// If approved, reduce stock
if ($status === 'Approved') {
    $stmt = $conn->prepare("SELECT vaccine_id FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->bind_result($vaccine_id);
    $stmt->fetch();
    $stmt->close();

    $conn->query("UPDATE vaccines SET quantity = quantity - 1 WHERE id = $vaccine_id");
}

// Update appointment status
$stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $appointment_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
$stmt->close();
$conn->close();
?>
