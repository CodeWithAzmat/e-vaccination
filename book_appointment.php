<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

// Get user_id and role from session
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// Validate role
if (!$user_id || $role !== 'parent') {
    echo json_encode(["success" => false, "message" => "Unauthorized. Only parents can book appointments."]);
    exit;
}

// Get input values
$child_id = $_POST['child_id'] ?? null;
$hospital_id = $_POST['hospital_id'] ?? null;
$vaccine_id = $_POST['vaccine_id'] ?? null;
$appointment_date = $_POST['appointment_date'] ?? null;
$time_slot = $_POST['time_slot'] ?? '10:00 AM';

// Validate inputs
if (!$child_id || !$hospital_id || !$vaccine_id || !$appointment_date) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// 🔍 Lookup parent.id using user_id
$parent_lookup = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
$parent_lookup->bind_param("i", $user_id);
$parent_lookup->execute();
$result = $parent_lookup->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid parent. Please complete your parent profile during signup."
    ]);
    exit;
}

$parent_row = $result->fetch_assoc();
$parent_id = $parent_row['id'];

// ✅ Now insert appointment
$stmt = $conn->prepare("INSERT INTO appointments (child_id, parent_id, hospital_id, vaccine_id, appointment_date, time_slot) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiss", $child_id, $parent_id, $hospital_id, $vaccine_id, $appointment_date, $time_slot);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Appointment booked successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
}
?>
