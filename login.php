<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Get raw JSON input
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

// Extract and sanitize input
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';
$role = isset($data['role']) ? trim($data['role']) : '';

// Validate inputs
if (empty($email) || empty($password) || empty($role)) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? AND role = ?");
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;

        // 👨‍👧 If parent, fetch and store parent_id
        if ($role === 'parent') {
            $parentQuery = $conn->prepare("SELECT id FROM parents WHERE user_id = ?");
            $parentQuery->bind_param("i", $user_id);
            $parentQuery->execute();
            $parentQuery->bind_result($parent_id);
            
            if ($parentQuery->fetch()) {
                $_SESSION['parent_id'] = $parent_id;
            } else {
                echo json_encode(["success" => false, "message" => "Parent record not found. Please complete profile."]);
                $parentQuery->close();
                exit;
            }

            $parentQuery->close();
        }

        // Define redirect page
        $redirectPage = '';
        if ($role === 'admin') {
            $redirectPage = 'admin.php';
        } elseif ($role === 'parent') {
            $redirectPage = 'parent.php';
        } elseif ($role === 'hospital') {
            $redirectPage = 'hospital.php';
        }

        echo json_encode(["success" => true, "redirect" => $redirectPage]);
    } else {
        echo json_encode(["success" => false, "message" => "Incorrect password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
