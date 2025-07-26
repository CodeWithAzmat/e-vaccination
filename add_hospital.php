<?php
include('db.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collecting form data
    $hospital_name = $_POST['hospital_name'] ?? '';
    $location = $_POST['location'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    // validation
    if (!empty($hospital_name) && !empty($location) && !empty($contact) && !empty($email) && !empty($address)) {

        
        $query = "INSERT INTO hospitals (hospital_name, location, contact_number, email, address) 
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sssss", $hospital_name, $location, $contact, $email, $address);

            if ($stmt->execute()) {
                header("Location: admin.php"); exit;
            } else {
                echo "❌ Database Execution Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "❌ Prepare Statement Failed: " . $conn->error;
        }

    } else {
        echo "⚠️ Please fill all fields.";
    }
}
?>
