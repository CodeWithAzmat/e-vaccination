<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header('Location: parent.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id = isset($_POST['child_id']) ? intval($_POST['child_id']) : 0;
    $name = isset($_POST['child_name']) ? trim($_POST['child_name']) : '';
    $age = isset($_POST['child_age']) ? intval($_POST['child_age']) : 0;
    $gender = isset($_POST['child_gender']) ? trim($_POST['child_gender']) : '';
    $dob = isset($_POST['child_dob']) ? $_POST['child_dob'] : '';
    // Optionally handle image upload here

    if ($child_id && $name && $age && $gender && $dob) {
        $stmt = $conn->prepare("UPDATE children SET name=?, age=?, gender=?, dob=? WHERE id=? AND parent_id=?");
        $stmt->bind_param("sissii", $name, $age, $gender, $dob, $child_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            header('Location: parent.php');
            exit;
        } else {
            echo "<script>alert('Update failed.');window.location='parent.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Missing required fields.');window.location='parent.php';</script>";
    }
    $conn->close();
    exit;
}

// If GET, fetch child data for editing
if (isset($_GET['child_id'])) {
    $child_id = intval($_GET['child_id']);
    $stmt = $conn->prepare("SELECT id, name, age, gender, dob FROM children WHERE id=? AND parent_id=?");
    $stmt->bind_param("ii", $child_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $child = $result->fetch_assoc();
        // Show edit form
        echo '<!DOCTYPE html>';
        echo '<html lang="en">';
        echo '<head><meta charset="UTF-8"><title>Edit Child</title>';
        echo '<link rel="stylesheet" href="style.css">';
        echo '<link rel="stylesheet" href="parent.css">';
        echo '</head><body style="background-color:lightblue;">';
        echo '<div class="container" style="display:flex;flex-direction:column; justify-content:center;align-items:center;">';
        echo '<h2>Edit Child</h2>';
        echo '<form action="edit_child.php" method="post" style="display:flex; flex-direction:column;  background-color:lightgreen;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);padding:20px 60px;border-radius:20px;
">';
        echo '<input type="hidden" name="child_id" value="' . htmlspecialchars($child['id']) . '">';
        echo '<label style="font-weight:bolder;">Name:</label><input style="padding:10px 20px; border:none;border-radius:10px; background-color:#e8fofe;" type="text" name="child_name" value="' . htmlspecialchars($child['name']) . '" required><br>';
        echo '<label style="font-weight:bolder;">Age:</label><input style="padding:10px 20px; border:none;border-radius:10px; background-color:#e8fofe;"type="number" name="child_age" value="' . htmlspecialchars($child['age']) . '" required><br>';
        echo '<label style="font-weight:bolder;">Gender:</label>';
        echo '<select name="child_gender" required style="padding:10px 20px; border:none;border-radius:10px; background-color:#e8fofe;">';
        echo '<option value="Male"' . ($child['gender'] == 'Male' ? ' selected' : '') . '>Male</option>';
        echo '<option value="Female"' . ($child['gender'] == 'Female' ? ' selected' : '') . '>Female</option>';
        echo '</select><br>';
        echo '<label style="font-weight:bolder;">Date of Birth:</label><input style="padding:10px 20px; border:none;border-radius:10px; background-color:#e8fofe;"type="date" name="child_dob" value="' . htmlspecialchars($child['dob']) . '" required><br>';
        echo '<button style="width:130px" type="submit" class="save-btn">Update Child</button> ';
        echo "<br>";
        echo '<a href="parent.php" style="width:130px;text-decoration:none;" class="cancel-btn">Cancel</a>';
        
        echo '</form>';
        echo '</div>';
        echo '</body></html>';
    } else {
        echo '<script>alert("Child not found.");window.location="parent.php";</script>';
    }
    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request."]);
