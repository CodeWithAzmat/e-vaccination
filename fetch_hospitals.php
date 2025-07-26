<?php
include('db.php');

$query = "SELECT id, hospital_name, location, contact_number, email, address FROM hospitals";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['hospital_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
        echo "<td>";
        echo '<a href="edit_hospital.php?id=' . $row['id'] . '"><button class="action-btn edit">Edit</button></a> ';
        echo '<a href="delete_hospital.php?id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this hospital?\');"><button class="action-btn delete">Delete</button></a>';
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No hospitals found.</td></tr>";
}
?>
