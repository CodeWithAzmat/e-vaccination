<?php
session_start();
$timeout_duration = 20000; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: index.html?timeout=1");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html?login=required");
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Panel - E-Vaccination System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="hospital.css">
    <style>
    </style>
</head>
<body>
    <div class="container">

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>E-Vaccination</h3>
            </div>
            <ul class="sidebar-menu">
                <li class="active" data-section="hospital-dashboard">
                    <a href="#dashboard-section">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li data-section="appointments-section">
                    <a href="#appointments-section">
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li data-section="vaccine-stock">
                    <a href="#vaccine-status-section">
                        <i class="fas fa-syringe"></i>
                        <span>Vaccine Stock</span>
                    </a>
                </li>

                <li data-section="add-vaccine">
                    <a href="#">
                        <i class="fas fa-syringe"></i>
                        <span>Add Vaccine</span>
                    </a>
                </li>

                 <li data-section="hospital-profile">
                    <a href="#">
                        <i class="fas fa-hospital"></i>
                        <span>Hospital Profile</span>
                    </a>
                </li>

                <li>
                    <a href="index.html">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-left">
                    <h2 id="pageTitle">Dashboard</h2>
                </div>
                <div class="header-right">
                    <div class="user-profile" id="userProfile">
<?php
require 'db.php';

$hospital_user_id = $_SESSION['user_id'] ?? null;
$hospital_image = 'default-user.png'; // Default fallback

if ($hospital_user_id) {
    $stmt = $conn->prepare("SELECT image FROM hospitals WHERE user_id = ?");
    $stmt->bind_param("i", $hospital_user_id);
    $stmt->execute();
    $stmt->bind_result($fetched_image);
    if ($stmt->fetch() && !empty($fetched_image)) {
        $hospital_image = $fetched_image;
    }
    $stmt->close();
}
?>

<!-- Show the image -->
<img src="<?= htmlspecialchars($hospital_image) ?>" alt="Hospital Image" style="width: 80px; height:80px; border-radius: 50%;">

                        <div class="dropdown-menu" id="userDropdown">
                            <a><i class="fas fa-user"></i> Profile</a>
                            <a href="index.html" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content">

                <!-- hospital dashboard section -->
                <div class="content-section active" id="hospital-dashboard">
                    <h1>Hospital Dashboard</h1>
                    <div class="hospital-content-1">
                        <div class="content-1">
                            <h2>Vaccines In Stock</h2>
                            <p>
                                <?php
                                include('db.php');
                                $stockQuery = "SELECT SUM(quantity) AS total_stock FROM vaccines";
                                $stockResult = mysqli_query($conn, $stockQuery);
                                $stockRow = mysqli_fetch_assoc($stockResult);
                                echo $stockRow['total_stock'] ?? 0;
                                ?>
                            </p>
                        </div>
                        <div class="content-1">
                            <h2>Total Patients</h2>
                            <p>
                                <?php
                                if (!isset($conn)) {
                                    include('db.php');
                                }
                                $patientQuery = "SELECT COUNT(DISTINCT child_id) AS total_patients FROM appointments WHERE status = 'Approved'";
                                $patientResult = mysqli_query($conn, $patientQuery);
                                if (!$patientResult) {
                                    echo '<span style="color:red">SQL Error: ' . mysqli_error($conn) . '</span>';
                                } else {
                                    $patientRow = mysqli_fetch_assoc($patientResult);
                                    echo $patientRow['total_patients'] ?? 0;
                                }
                                ?>
                            </p>
                        </div>
                        <div class="content-1">
                            <h2>Recently vaccinated</h2>
                            <p>
                                <?php
                                include('db.php');
                                $recentCountQuery = "SELECT COUNT(*) as cnt FROM (SELECT a.id FROM appointments a WHERE a.status = 'Approved' ORDER BY a.appointment_date DESC, a.id DESC LIMIT 5) as recent";
                                $recentCountResult = mysqli_query($conn, $recentCountQuery);
                                if ($recentCountResult) {
                                    $recentCountRow = mysqli_fetch_assoc($recentCountResult);
                                    echo $recentCountRow['cnt'] ?? 0;
                                } else {
                                    echo '0';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="hospital-content-2">
                        <div class="content-1">
                            <h2>Recently Vaccinated Patients</h2>
                            <form action="#" method="post">
                                <table>
                                    <thead>
                                        <tr>
                                            <td>Patient</td>
                                            <td>Date</td>
                                            <td>Time</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include('db.php');
                                        $recentQuery = "SELECT a.appointment_date, a.time_slot, c.name as child_name FROM appointments a JOIN children c ON a.child_id = c.id WHERE a.status = 'Approved' ORDER BY a.appointment_date DESC, a.id DESC LIMIT 5";
                                        $recentResult = mysqli_query($conn, $recentQuery);
                                        if ($recentResult && mysqli_num_rows($recentResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($recentResult)) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($row['child_name']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['appointment_date']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['time_slot']) . '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="3">No recently vaccinated patients.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="content-1">
                            <h2>Vaccines In Stock</h2>
                            <form action="#" method="post">
                                <table>
                                    <thead>
                                        <tr>
                                            <td>Vaccine</td>
                                            <td>Stock</td>
                                            <td>Status</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        include('db.php');

                                        $query = "SELECT vaccine_name, quantity FROM vaccines";
                                        $result = mysqli_query($conn, $query);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $status = $row['quantity'] < 5 ? 'Low Stock' : 'Available';
                                                $color = $row['quantity'] < 5 ? 'red' : 'green';
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['vaccine_name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                                echo "<td style='color: $color; font-weight: bold;'>" . $status . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3'>No vaccine data found.</td></tr>";
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="content-section" id="appointments-section">
                    <h1>Appointment Section</h1>
                    <?php
                    require 'db.php';


                    $hospital_user_id = $_SESSION['user_id'] ?? null;

                    if (!$hospital_user_id) {
                        echo "<p style='color:red;'>Unauthorized access.</p>";
                        exit;
                    }

                    // Get hospital ID
                    $stmt = $conn->prepare("SELECT id FROM hospitals WHERE user_id = ?");
                    $stmt->bind_param("i", $hospital_user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows == 0) {
                        echo "<p style='color:red;'>Hospital not found.</p>";
                        exit;
                    }
                    $hospital = $result->fetch_assoc();
                    $hospital_id = $hospital['id'];

                    // Get today's appointments
                    $stmt = $conn->prepare("
        SELECT a.id, c.name AS child_name, v.vaccine_name, a.appointment_date, a.time_slot, a.status
        FROM appointments a
        JOIN children c ON a.child_id = c.id
        JOIN vaccines v ON a.vaccine_id = v.id
        WHERE a.hospital_id = ? AND DATE(a.appointment_date) = CURDATE()
    ");
                    $stmt->bind_param("i", $hospital_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 0) {
                        echo "<p style='color:red;'>No appointments for today.</p>";
                    } else {
                        echo '<table border="1" cellpadding="8">
                <tr>
                    <th>ID</th>
                    <th>Child</th>
                    <th>Vaccine</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Status</th>
                </tr>';
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr class="appointment-row">';
                            echo '<td class="appointment-cell" data-label="ID">' . htmlspecialchars($row['id']) . '</td>';
                            echo '<td class="appointment-cell" data-label="Child Name">' . htmlspecialchars($row['child_name']) . '</td>';
                            echo '<td class="appointment-cell" data-label="Vaccine">' . htmlspecialchars($row['vaccine_name']) . '</td>';
                            echo '<td class="appointment-cell" data-label="Date">' . htmlspecialchars($row['appointment_date']) . '</td>';
                            echo '<td class="appointment-cell" data-label="Time Slot">' . htmlspecialchars($row['time_slot']) . '</td>';

                            // Status cell
                            $statusClass = strtolower($row['status']);
                            echo '<td class="appointment-cell status ' . $statusClass . '" data-label="Status">' . htmlspecialchars($row['status']) . '</td>';

                           
                            if ($row['status'] === 'Pending') {
                                echo '<form method="POST" action="update_appointment_status.php" class="status-form">
                <input type="hidden" name="appointment_id" value="' . $row['id'] . '">

              </form>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        };

                        echo '</table>';
                    };
                    ?>
                </div>

                <!-- Vccince Stock  -->
                <div id="vaccine-stock" class="content-section">
                    <h2>Vaccine Stock</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Vaccine Name</th>
                                <th>Disease Prevented</th>
                                <th>Recommended Age</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start();
                            }
                            include 'db.php';

                            $hospital_id = $_SESSION['user_id'] ?? null;

                            if (!$hospital_id) {
                                echo "<tr><td colspan='5'>❌ Hospital not logged in.</td></tr>";
                                exit;
                            }

                            // Handle quantity update or insert
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
                                $vaccine_id = intval($_POST['vaccine_id']);
                                $new_quantity = intval($_POST['new_quantity']);

                                // Check if vaccine exists for this hospital
                                $checkSql = "SELECT quantity FROM vaccines WHERE id = ? AND hospital_id = ?";
                                $checkStmt = $conn->prepare($checkSql);
                                $checkStmt->bind_param("ii", $vaccine_id, $hospital_id);
                                $checkStmt->execute();
                                $checkResult = $checkStmt->get_result();

                                if ($checkResult->num_rows > 0) {
                                    // Vaccine exists: update quantity
                                    $row = $checkResult->fetch_assoc();
                                    $current_quantity = $row['quantity'];
                                    $updated_quantity = $current_quantity + $new_quantity;

                                    $updateSql = "UPDATE vaccines SET quantity = ? WHERE id = ? AND hospital_id = ?";
                                    $updateStmt = $conn->prepare($updateSql);
                                    $updateStmt->bind_param("iii", $updated_quantity, $vaccine_id, $hospital_id);
                                    $updateStmt->execute();
                                } else {
                                    echo "<tr><td colspan='5'>❌ Vaccine not found for this hospital.</td></tr>";
                                }
                            }

                            // Fetch vaccine stock for this hospital
                            $sql = "SELECT id, vaccine_name, disease_prevented, recommended_age, quantity 
        FROM vaccines 
        WHERE hospital_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $hospital_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['vaccine_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['disease_prevented']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['recommended_age']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                    echo "<td>
            <form method='POST'>
                <input type='hidden' name='vaccine_id' value='" . $row['id'] . "'>
                <input type='number' name='new_quantity' placeholder='Add Qty' min='0' required>
                <button type='submit' name='update_quantity'>Update</button>
            </form>
        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>⚠️ No vaccine stock found for this hospital.</td></tr>";
                            }

                            $conn->close();
                            ?>

                        </tbody>
                    </table>
                </div>


                <div class="content-section" id="add-vaccine">

                    <h3>Add New Vaccine</h3>
                    <form method="POST" action="add_vaccine_hospital.php">
                        <input type="text" name="vaccine_name" placeholder="Vaccine Name" required>
                        <input type="text" name="disease_prevented" placeholder="Disease Prevented" required>
                        <input type="text" name="recommended_age" placeholder="Recommended Age" required>
                        <input type="number" name="quantity" placeholder="Initial Quantity" min="0" required>
                        <button type="submit" name="add_vaccine">Add Vaccine</button>
                    </form>
                </div>


                <?php

require 'db.php';

$hospital_user_id = $_SESSION['user_id'] ?? null;

$hospital_name = "N/A";
$hospital_address = "N/A";
$hospital_contact = "N/A";
$hospital_image = "uploads/default_hospital.jpg"; // default image

if ($hospital_user_id) {
    
    $stmt = $conn->prepare("SELECT hospital_name, address, contact_number, image FROM hospitals WHERE user_id = ?");
    $stmt->bind_param("i", $hospital_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $hospital_name = htmlspecialchars($row['hospital_name']);
        $hospital_address = htmlspecialchars($row['address']);
        $hospital_contact = htmlspecialchars($row['contact_number']);

        if (!empty($row['image']) && file_exists($row['image'])) {
            $hospital_image = $row['image'];
        }
    }
    $stmt->close();
}
?>


      <div class="content-section" id="hospital-profile">
    <h1>Hospital Profile</h1>
    <br><br>
    <p><strong>Hospital Name:</strong> <?= $hospital_name ?></p>
    <p><strong>Hospital Address:</strong> <?= $hospital_address ?></p>
    <p><strong>Hospital Contact:</strong> <?= $hospital_contact ?></p>

    <!-- Hospital Image Upload -->
    <form method="POST" action="upload_hospital_image.php" enctype="multipart/form-data">
        <div class="image-upload-container" onclick="document.getElementById('imageInput').click();">
            <img style="border-radius:50%;" src="<?= htmlspecialchars($hospital_image) ?>" alt="Hospital Image">
            <input type="file" name="hospital_image" id="imageInput" onchange="this.form.submit();">
        </div>
    </form>

    <!-- Toggle Button -->
    <button id="toggleUpdateBtn" class="toggle-btn">Update Info</button>

    <!-- Update Form (Initially Hidden) -->
    <div class="update-form-container" id="updateForm" style="display: none;">
        <h2>Update Hospital Information</h2>
        <form method="POST" action="update_hospital_info.php">
            <input type="text" name="hospital_name" placeholder="Hospital Name" value="<?= htmlspecialchars($hospital_name) ?>" required>
            <input type="text" name="hospital_address" placeholder="Hospital Address" value="<?= htmlspecialchars($hospital_address) ?>" required>
            <input type="text" name="hospital_contact" placeholder="Hospital Contact" value="<?= htmlspecialchars($hospital_contact) ?>" required>
            <button type="submit">Update Info</button>
        </form>
    </div>
</div>

            </div>
        </div>
    </div>
    <script src="hospital.js"></script>

    <script>
    // Toggle update form for hospital profile
    document.getElementById('toggleUpdateBtn').addEventListener('click', function () {
        const form = document.getElementById('updateForm');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            this.textContent = 'Hide Form';
        } else {
            form.style.display = 'none';
            this.textContent = 'Update Info';
        }
    });

    // Section switching logic
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarItems = document.querySelectorAll('.sidebar-menu li[data-section]');
        const sections = document.querySelectorAll('.content-section');

        sidebarItems.forEach(function (item) {
            item.addEventListener('click', function () {
                // Remove active from all sidebar items
                sidebarItems.forEach(function (el) { el.classList.remove('active'); });
                // Add active to clicked item
                item.classList.add('active');

                // Hide all sections
                sections.forEach(function (section) { section.classList.remove('active'); });
                // Show the selected section
                const sectionId = item.getAttribute('data-section');
                const section = document.getElementById(sectionId);
                if (section) {
                    section.classList.add('active');
                }
            });
        });

        // On page load, show dashboard if no section is active
        let anyActive = false;
        sections.forEach(function (section) {
            if (section.classList.contains('active')) {
                anyActive = true;
            }
        });
        if (!anyActive && sections.length > 0) {
            sections[0].classList.add('active');
        }
    });
    </script>
</body>
</html>