<?php
require 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: admin.php?error=invalid_id");
    exit;
}

// Step 1: Fetch vaccine_id from appointment
$stmt = $conn->prepare("SELECT vaccine_id FROM appointments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($vaccine_id);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: admin.php?error=appointment_not_found");
    exit;
}
$stmt->close();

// Step 2: Fetch vaccine quantity
$stmt2 = $conn->prepare("SELECT quantity FROM vaccines WHERE id = ?");
$stmt2->bind_param("i", $vaccine_id);
$stmt2->execute();
$stmt2->bind_result($quantity);
if (!$stmt2->fetch()) {
    $stmt2->close();
    header("Location: admin.php?error=vaccine_not_found");
    exit;
}
$stmt2->close();

// Step 3: Check stock
if ($quantity <= 0) {
    header("Location: admin.php?error=out_of_stock");
    exit;
}

// Step 4: Approve appointment and reduce quantity
$update_vaccine = $conn->prepare("UPDATE vaccines SET quantity = quantity - 1 WHERE id = ?");
$update_vaccine->bind_param("i", $vaccine_id);
$update_vaccine->execute();
$update_vaccine->close();

$update_appointment = $conn->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
$update_appointment->bind_param("i", $id);
$update_appointment->execute();
$update_appointment->close();

header("Location: admin.php?approval=success");
exit;
?>
