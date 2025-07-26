<?php
require 'db.php';

$response = [
    'success' => false,
    'hospitals' => [],
    'vaccines' => []
];

// Fetch hospitals
$hospitalResult = $conn->query("SELECT id, hospital_name, address FROM hospitals");
if ($hospitalResult) {
    while ($row = $hospitalResult->fetch_assoc()) {
        $response['hospitals'][] = $row;
    }
}

// Fetch vaccines with quantity > 0
$vaccineResult = $conn->query("SELECT id, vaccine_name FROM vaccines WHERE quantity > 0");
if ($vaccineResult) {
    while ($row = $vaccineResult->fetch_assoc()) {
        $response['vaccines'][] = $row;
    }
}

$response['success'] = true;
echo json_encode($response);
