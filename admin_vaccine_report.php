<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Denied. Admins only.'); window.location.href='index.html';</script>";
    exit;
}

$query = "SELECT h.hospital_name, v.vaccine_name, v.disease_prevented, v.recommended_age, v.quantity
          FROM vaccines v
          JOIN hospitals h ON v.hospital_id = h.id
          ORDER BY h.hospital_name ASC";

$result = mysqli_query($conn, $query);

echo "<h2>🩺 Hospital Vaccine Report</h2>";
echo "<table border='1' cellpadding='10'>
        <tr>
            <th>Hospital</th>
            <th>Vaccine</th>
            <th>Disease Prevented</th>
            <th>Recommended Age</th>
            <th>Quantity</th>
        </tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>" . htmlspecialchars($row['hospital_name']) . "</td>
            <td>" . htmlspecialchars($row['vaccine_name']) . "</td>
            <td>" . htmlspecialchars($row['disease_prevented']) . "</td>
            <td>" . htmlspecialchars($row['recommended_age']) . "</td>
            <td>" . htmlspecialchars($row['quantity']) . "</td>
          </tr>";
}

echo "</table>";
?>
