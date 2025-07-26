<?php
include('db.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

//  Load input
$data = json_decode(file_get_contents("php://input"), true);
$question = isset($data['question']) ? trim(strtolower($data['question'])) : '';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent' || $question === '') {
    echo json_encode(["reply" => " Please enter a valid question or login again."]);
    exit();
}

$parent_id = intval($_SESSION['user_id']);

//  Check if question is about upcoming vaccines
$checkKeywords = ['upcoming', 'next vaccine', 'due vaccine', 'future vaccine'];
$isUpcomingQuery = false;
foreach ($checkKeywords as $keyword) {
    if (strpos($question, $keyword) !== false) {
        $isUpcomingQuery = true;
        break;
    }
}

if ($isUpcomingQuery) {
    //  Get all children of the logged-in parent
    $childrenQuery = $conn->query("SELECT id, name FROM children WHERE parent_id = $parent_id");
    
    if (!$childrenQuery || $childrenQuery->num_rows === 0) {
        echo json_encode(["reply" => " No child found for your account."]);
        exit();
    }

    $reply = "Upcoming Vaccines:\n";
    $hasVaccine = false;

    while ($child = $childrenQuery->fetch_assoc()) {
        $child_id = intval($child['id']);
        $child_name = $child['name'];

        $vaccineQuery = $conn->query("SELECT vaccine_name, due_date FROM vaccines WHERE child_id = $child_id AND due_date >= CURDATE() ORDER BY due_date ASC");

        if ($vaccineQuery && $vaccineQuery->num_rows > 0) {
            $reply .= " Child: $child_name\n";
            while ($vaccine = $vaccineQuery->fetch_assoc()) {
                $reply .= "- " . $vaccine['vaccine_name'] . " on " . $vaccine['due_date'] . "\n";
            }
            $hasVaccine = true;
        }
    }

    if ($hasVaccine) {
        echo json_encode(["reply" => $reply]);
        exit();
    } else {
        echo json_encode(["reply" => " No upcoming vaccines found for your children."]);
        exit();
    }
}


$apiKey = "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";


$firstChild = $conn->query("SELECT id, name FROM children WHERE parent_id = $parent_id LIMIT 1")->fetch_assoc();
$child_id = $firstChild['id'] ?? 'N/A';
$child_name = $firstChild['name'] ?? 'Unknown';

$payload = json_encode([
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "You are a helpful assistant for a child vaccination system."],
        ["role" => "user", "content" => "Parent (child ID $child_id, name $child_name) asked: $question"]
    ]
]);

$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["reply" => "API Error: " . curl_error($ch)]);
    curl_close($ch);
    exit();
}
curl_close($ch);

$responseData = json_decode($response, true);
if (isset($responseData['choices'][0]['message']['content'])) {
    echo json_encode(["reply" => $responseData['choices'][0]['message']['content']]);
} elseif (isset($responseData['error']['message'])) {
    echo json_encode(["reply" => " API Error: " . $responseData['error']['message']]);
} else {
    echo json_encode(["reply" => " Could not generate a response."]);
}
?>
