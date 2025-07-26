<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id = $_POST['child_id'] ?? null;
    $parent_id = $_POST['parent_id'] ?? null;
    $hospital_id = $_POST['hospital_id'] ?? null;
    $vaccine_id = $_POST['vaccine_id'] ?? null;
    $appointment_date = $_POST['appointment_date'] ?? null;
    $time_slot = $_POST['time_slot'] ?? '10:00 AM';

    if (!$child_id || !$parent_id || !$hospital_id || !$vaccine_id || !$appointment_date) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("INSERT INTO appointments (child_id, parent_id, hospital_id, vaccine_id, appointment_date, time_slot) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiss", $child_id, $parent_id, $hospital_id, $vaccine_id, $appointment_date, $time_slot);

    if ($stmt->execute()) {
        header("Location: parent.php?booking=success");
    } else {
        echo "Failed to book: " . $stmt->error;
    }
}
?>
