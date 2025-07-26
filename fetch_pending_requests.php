<?php
require 'db.php';

$sql = "SELECT 
    a.id,
    c.name AS child_name,
    v.vaccine_name,
    a.appointment_date,
    a.time_slot,
    h.hospital_name,
    a.status
FROM appointments a
JOIN children c ON a.child_id = c.id
JOIN vaccines v ON a.vaccine_id = v.id
JOIN hospitals h ON a.hospital_id = h.id
WHERE a.status = 'Pending'
ORDER BY a.created_at DESC";

$result = $conn->query($sql);
?>

<tbody>
<?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['child_name']) ?></td>
        <td><?= htmlspecialchars($row['vaccine_name']) ?></td>
        <td><?= htmlspecialchars($row['appointment_date']) ?></td>
        <td><?= htmlspecialchars($row['time_slot']) ?></td>
        <td><?= htmlspecialchars($row['hospital_name']) ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td>
            <button class="action-btn view">View</button>
            <button class="action-btn approve">Approve</button>
            <button class="action-btn reject">Reject</button>
        </td>
    </tr>
<?php } ?>
</tbody>
