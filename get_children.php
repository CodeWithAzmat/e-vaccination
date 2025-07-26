<?php
header('Content-Type: application/json');
require 'db.php';

if (!isset($_POST['parent_id'])) {
    echo json_encode(["success" => false, "message" => "Parent ID is required"]);
    exit;
}

$parent_id = $_POST['parent_id'];

$sql = "SELECT * FROM children WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

echo json_encode(["success" => true, "data" => $children]);
?>
