<?php
require 'db.php';

// Handle filters
$status = $_GET['status'] ?? '';
$vaccine = $_GET['vaccine'] ?? '';
$search = $_GET['search'] ?? '';

// Build SQL
$sql = "SELECT 
            a.id,
            c.name AS child_name,
            p.name AS parent_name,
            v.vaccine_name,
            v.disease_prevented,
            h.hospital_name,
            a.appointment_date,
            a.time_slot,
            a.status
        FROM appointments a
        JOIN children c ON a.child_id = c.id
        JOIN parents p ON a.parent_id = p.id
        JOIN vaccines v ON a.vaccine_id = v.id
        JOIN hospitals h ON a.hospital_id = h.id
        WHERE 1";

if (!empty($status)) {
    $sql .= " AND a.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}

if (!empty($vaccine)) {
    $sql .= " AND v.vaccine_name = '" . mysqli_real_escape_string($conn, $vaccine) . "'";
}

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (c.name LIKE '%$search%' OR p.name LIKE '%$search%')";
}

$sql .= " ORDER BY a.appointment_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!-- Now start your HTML -->
<section class="content-section" id="vaccination-report">
    <h1>Vaccination Report</h1>

    <!-- Filter Form -->
    <form class="filters" method="GET">
        <input type="text" name="search" placeholder="Search by Child or Parent" value="<?= htmlspecialchars($search) ?>">
        <!-- etc... -->
    </form>

    <!-- Table & More HTML Below -->
</section>
