<?php
session_start();
require 'db.php';

// Check if parent logged
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    http_response_code(403); 
    echo "❌ Unauthorized access. Please log in as parent.";
    exit;
}

$parent_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['child_name'] ?? '');
    $age = intval($_POST['child_age'] ?? 0);
    $dob = $_POST['child_dob'] ?? '';
    $gender = $_POST['child_gender'] ?? '';
    $image_url = '';

    if (!$name || !$dob || !$gender || !isset($_FILES['child_photo'])) {
        echo "❌ Please fill all required fields.";
        exit;
    }
//checking parent panel existing
    $check = $conn->prepare("SELECT id FROM parents WHERE id = ?");
    $check->bind_param("i", $parent_id);
    $check->execute();
    $result = $check->get_result();

    // record insert if not
    if ($result->num_rows === 0) {
        $insert_parent = $conn->prepare("INSERT INTO parents (id) VALUES (?)");
        $insert_parent->bind_param("i", $parent_id);

        if (!$insert_parent->execute()) {
            echo "❌ Failed to create parent record: " . $insert_parent->error;
            exit;
        }
    }

    //image upload code 
    if ($_FILES['child_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES['child_photo']['tmp_name'];
        $fileName = basename($_FILES['child_photo']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowed)) {
            echo "❌ Invalid image type. Allowed types: jpg, jpeg, png, gif.";
            exit;
        }

        $uniqueName = time() . '_' . uniqid() . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $uniqueName;

        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            $image_url = $targetFilePath;
        } else {
            echo "❌ Failed to upload image.";
            exit;
        }
    } else {
        echo "❌ Error in file upload.";
        exit;
    }

    
    $stmt = $conn->prepare("INSERT INTO children (name, age, dob, gender, image_url, parent_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssi", $name, $age, $dob, $gender, $image_url, $parent_id);

    if ($stmt->execute()) {
        header("Location: parent.php?success=child_added");
        exit;
    } else {
        echo "❌ Database error: " . $stmt->error;
    }


    $stmt->close();
    $conn->close();
} else {
    echo "❌ Invalid request method.";
}
?>
