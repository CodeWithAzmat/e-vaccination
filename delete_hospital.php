<?php
// delete_hospital.php
include('db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color:red;'>Invalid hospital ID.</p>";
    exit;
}

$hospital_id = intval($_GET['id']);

// Optional: Check for related records before deleting (appointments, vaccines, etc.)
// You may want to add logic to prevent deletion if there are dependencies.

$stmt = $conn->prepare("DELETE FROM hospitals WHERE id = ?");
$stmt->bind_param("i", $hospital_id);

if ($stmt->execute()) {
    echo "<script>alert('Hospital deleted successfully.'); window.location.href='admin.php#hospital-list';</script>";
} else {
    echo "<p style='color:red;'>Failed to delete hospital. Please try again.</p>";
}
$stmt->close();
$conn->close();
?>
