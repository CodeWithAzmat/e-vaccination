<?php
include('db.php');

// Handle form submission
if (isset($_POST['submit'])) {
    $id = intval($_POST['id']);
    $vaccine_name = $_POST['vaccine_name'];
    $disease_prevented = $_POST['disease_prevented'];
    $recommended_age = $_POST['recommended_age'];
    $quantity = $_POST['total_doses'];

    $sql = "UPDATE vaccines SET 
                vaccine_name = '$vaccine_name',
                disease_prevented = '$disease_prevented',
                recommended_age = '$recommended_age',
                quantity = '$quantity'
            WHERE id = $id";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('Vaccine updated successfully!'); window.location.href='admin.php';</script>";
        exit;
    } else {
        echo "<script>alert('Update failed: " . mysqli_error($conn) . "');</script>";
    }
}

// Show form
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM vaccines WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Vaccine not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Vaccine</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <h1>Edit Vaccine</h1>
    <form class="add-vaccine-form" action="edit_vaccine.php" method="POST">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">

        <div class="form-group">
            <label for="vaccine_name">Vaccine Name</label>
            <input type="text" id="vaccine_name" name="vaccine_name" value="<?= htmlspecialchars($row['vaccine_name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="disease_prevented">Disease Prevented</label>
            <input type="text" id="disease_prevented" name="disease_prevented" value="<?= htmlspecialchars($row['disease_prevented']) ?>" required>
        </div>

        <div class="form-group">
            <label for="recommended_age">Recommended Age</label>
            <input type="text" id="recommended_age" name="recommended_age" value="<?= htmlspecialchars($row['recommended_age']) ?>" required>
        </div>

        <div class="form-group">
            <label for="total_doses">Total Doses</label>
            <input type="number" id="total_doses" name="total_doses" value="<?= htmlspecialchars($row['quantity']) ?>" required>
        </div>

        <button type="submit" name="submit" class="submit-btn">Update Vaccine</button>
    </form>

</body>
</html>
