<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Use intval for safety

    $sql = "DELETE FROM vaccines WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Record deleted successfully'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href='admin.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request. No ID provided.'); window.location.href='admin.php';</script>";
}
?>
