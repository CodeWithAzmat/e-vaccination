<?php
// edit_hospital.php
include('db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color:red;'>Invalid hospital ID.</p>";
    exit;
}

$hospital_id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospital_name = $_POST['hospital_name'] ?? '';
    $location = $_POST['location'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    $stmt = $conn->prepare("UPDATE hospitals SET hospital_name=?, location=?, contact_number=?, email=?, address=? WHERE id=?");
    $stmt->bind_param("sssssi", $hospital_name, $location, $contact_number, $email, $address, $hospital_id);
    if ($stmt->execute()) {
        echo "<script>alert('Hospital updated successfully.'); window.location.href='admin.php#hospital-list';</script>";
        exit;
    } else {
        echo "<p style='color:red;'>Failed to update hospital. Please try again.</p>";
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT hospital_name, location, contact_number, email, address FROM hospitals WHERE id = ?");
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    // Show edit form
    ?>
    <h2>Edit Hospital</h2>
    <form method="POST">
        <label>Hospital Name:</label>
        <input type="text" name="hospital_name" value="<?= htmlspecialchars($row['hospital_name']) ?>" required><br>
        <label>Location:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($row['location']) ?>" required><br>
        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($row['contact_number']) ?>" required><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required><br>
        <label>Address:</label>
        <textarea name="address" required><?= htmlspecialchars($row['address']) ?></textarea><br>
        <button type="submit">Update Hospital</button>
    </form>
    <?php
} else {
    echo "<p style='color:red;'>Hospital not found.</p>";
}
$stmt->close();
$conn->close();
?>
