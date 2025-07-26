<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<script>alert('Access Denied. Please login.'); window.location.href='index.html';</script>";
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vaccine_name = trim($_POST['vaccine_name']);
    $disease_prevented = trim($_POST['disease_prevented']);
    $recommended_age = trim($_POST['recommended_age']);
    $quantity = isset($_POST['total_doses']) ? intval($_POST['total_doses']) : -1;

    if (empty($vaccine_name) || empty($disease_prevented) || empty($recommended_age) || $quantity < 0) {
        echo "<script>alert('Please fill in all fields with valid values.'); window.history.back();</script>";
        exit;
    }

    $hospital_id = ($role === 'hospital') ? $user_id : null;

    // Check if the vaccine already exists for the same hospital (or NULL for admin)
    $check_sql = "SELECT id, quantity FROM vaccines WHERE vaccine_name = ? AND 
                 " . ($role === 'hospital' ? "hospital_id = ?" : "hospital_id IS NULL");
    $stmt = $conn->prepare($check_sql);
    
    if ($role === 'hospital') {
        $stmt->bind_param("si", $vaccine_name, $hospital_id);
    } else {
        $stmt->bind_param("s", $vaccine_name);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ✅ Update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $vaccine_id = $row['id'];

        $update_stmt = $conn->prepare("UPDATE vaccines SET quantity = ?, disease_prevented = ?, recommended_age = ? WHERE id = ?");
        $update_stmt->bind_param("issi", $new_quantity, $disease_prevented, $recommended_age, $vaccine_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('✅ Vaccine quantity updated successfully.'); window.location.href='" . ($role === 'admin' ? 'admin.php' : 'hospital.php') . "';</script>";
            exit;
        } else {
            echo "<script>alert('❌ Error updating vaccine: " . $conn->error . "'); window.history.back();</script>";
            exit;
        }
    } else {
        // ❌ Insert new vaccine if not found
        $insert_sql = "INSERT INTO vaccines (vaccine_name, disease_prevented, recommended_age, quantity, hospital_id)
                       VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssii", $vaccine_name, $disease_prevented, $recommended_age, $quantity, $hospital_id);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Vaccine added successfully.'); window.location.href='" . ($role === 'admin' ? 'admin.php' : 'hospital.php') . "';</script>";
            exit;
        } else {
            echo "<script>alert('❌ Error adding vaccine: " . $conn->error . "'); window.history.back();</script>";
            exit;
        }
    }
} else {
    echo "<h2>Access Denied. Invalid Request.</h2>";
}
?>
