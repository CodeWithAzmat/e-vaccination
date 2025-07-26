<?php
session_start();
$timeout_duration = 1800; 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();     
    session_destroy();   
    echo "<p style='color:red;'>Session timed out. Please login again.</p>";
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red;'>You are not logged in. Please login first.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - E-Vaccination System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>E-Vaccination</h3>
            </div>
            <ul class="sidebar-menu">
                <li class="active" data-section="dashboard-section">
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li data-section="children-section">
                    <a href="#">
                        <i class="fas fa-child"></i>
                        <span>All Child Details</span>
                    </a>
                </li>

                <li data-section="upcoming-vaccination">
                    <a href="#">
                        <i class="fas fa-syringe"></i>
                        <span>Upcoming Vaccination</span>
                    </a>
                </li>


                <li data-section="vaccination-report">
                    <a href="#">
                        <i class="fas fa-syringe"></i>
                        <span>Vaccination Report</span>
                    </a>
                </li>

                <li data-section="vaccine-list">
                    <a href="#">
                        <i class="fas fa-syringe"></i>
                        <span>Vaccine List</span>
                    </a>
                </li>

                <li data-section="hospital-list">
                    <a href="#">
                        <i class="fas fa-hospital"></i>
                        <span>Hospital List</span>
                    </a>
                </li>

                <li data-section="parent-list">
                    <a href="#">
                        <i class="fas fa-user-friends"></i>
                        <span>Parent List</span>
                    </a>
                </li>
                <li data-section="pending-request">
                    <a href="#">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Pending Request</span>
                    </a>
                </li>
                <li data-section="add-vaccine">
                    <a href="#">
                        <i class="fas fa-syringe"></i>
                        <span>Add Vaccine</span>
                    </a>
                </li>
                <li data-section="add-hospital">
                    <a href="#">
                        <i class="fas fa-hospital"></i>
                        <span>Add Hospital</span>
                    </a>
                </li>
                <li data-section="my-profile">
                    <a href="my-profile">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
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

        <!-- this is  Main Content div -->
        <div class="main-content">
            <!--this is  Header -->
            <div class="header">
                <div class="header-left">
                    <h2 id="pageTitle">Dashboard</h2>
                </div>
                <div class="header-right">
                    <div class="user-profile" id="userProfile">

                        <?php

                        require 'db.php';
                        $admin_id = 43;
                        $admin_image = 'uploads/default.jpg';

                        if ($admin_id) {
                            $stmt = $conn->prepare("SELECT image FROM users WHERE id = ?");
                            $stmt->bind_param("i", $admin_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($row = $result->fetch_assoc()) {
                                if (!empty($row['image']) && file_exists($row['image'])) {
                                    $admin_image = $row['image'];
                                }
                            }
                            $stmt->close();
                        }
                        ?>

                        <img src="<?= htmlspecialchars($admin_image) ?>" alt="Profile" style="border-radius: 50%; height:80px;width:80px;">

                        <div class="dropdown-menu" id="userDropdown">
                            <a href="#profile"><i class="fas fa-user"></i> Profile</a>
                            <a href="index.html" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- this is  Content Area div for all work -->
            <div class="content">
                <!-- this  Dashboard Section -->
                <div class="content-section active" id="dashboard-section">
                    <h1>Admin Dashboard</h1>
                    <div class="admin-content-1">
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
                    <div class="admin-content-2">
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
                                                echo '<td data-label="Child Name">' . htmlspecialchars($row['child_name']) . '</td>';
                                                echo '<td data-label="Appointment Date">' . htmlspecialchars($row['appointment_date']) . '</td>';
                                                echo '<td data-label="Time Slot">' . htmlspecialchars($row['time_slot']) . '</td>';
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
                                                echo '<td data-label="Vaccine Name">' . htmlspecialchars($row['vaccine_name']) . '</td>';
                                                echo '<td data-label="Quantity">' . htmlspecialchars($row['quantity']) . '</td>';
                                                echo '<td data-label="Status" style="color: ' . $color . '; font-weight: bold;">' . htmlspecialchars($status) . '</td>';

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
                <!-- All Child Details Section start from here -->
                <div class="content-section" id="children-section">
                    <h1>All Child Details</h1>
                    <div class="all-child-detail">
                        <table class="details-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Child Name</th>
                                    <th>Father Name</th>
                                    <th>DOB</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include('db.php');


                                $childQuery = "
    SELECT c.id, c.name AS child_name, c.dob, c.gender, p.name AS father_name
    FROM children c
    LEFT JOIN parents p ON c.parent_id = p.id
    ORDER BY c.id DESC
";

                                $childResult = mysqli_query($conn, $childQuery);

                                if ($childResult && mysqli_num_rows($childResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($childResult)) {
                                        $childId = $row['id'];
                                        $status = 'Pending';


                                        $checkApproved = mysqli_query($conn, "SELECT 1 FROM appointments WHERE child_id = '$childId' AND status = 'Approved' LIMIT 1");
                                        if ($checkApproved && mysqli_num_rows($checkApproved) > 0) {
                                            $status = 'Approved';
                                        } else {
                                            $checkRejected = mysqli_query($conn, "SELECT 1 FROM appointments WHERE child_id = '$childId' AND status = 'Rejected' LIMIT 1");
                                            if ($checkRejected && mysqli_num_rows($checkRejected) > 0) {
                                                $status = 'Rejected';
                                            }
                                        }

                                        $statusClass = strtolower($status);
                                        $fatherName = !empty($row['father_name']) ? htmlspecialchars($row['father_name']) : 'N/A';

                                        echo '<tr class="' . $statusClass . '">';
                                        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['child_name']) . '</td>';
                                        echo '<td>' . $fatherName . '</td>';
                                        echo '<td>' . htmlspecialchars($row['dob']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['gender']) . '</td>';
                                        echo '<td class="' . $statusClass . '">' . htmlspecialchars($status) . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6">No child records found.</td></tr>';
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!--All child details sectipon end here  -->

                <!-- Upcoming Vaccine section start here -->
                <div class="content-section" id="upcoming-vaccination">
                    <h1>Upcoming Vaccination</h1>
                    <div class="upcoming-vaccines-details">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Child Name</th>
                                    <th>Vaccine Name</th>
                                    <th>Due Date</th>
                                    <th>Hospital</th>
                                    <th>Time Slot</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include('db.php');

                                $today = date('Y-m-d');
                                $upcomingQuery = "SELECT a.id, c.name as child_name, v.vaccine_name, a.appointment_date, h.hospital_name, a.time_slot, a.status
                                    FROM appointments a
                                    JOIN children c ON a.child_id = c.id
                                    JOIN vaccines v ON a.vaccine_id = v.id
                                    JOIN hospitals h ON a.hospital_id = h.id
                                    WHERE (a.status = 'Pending' OR a.status = 'Approved') AND a.appointment_date >= '$today'
                                    ORDER BY a.appointment_date ASC, a.id ASC";
                                $upcomingResult = mysqli_query($conn, $upcomingQuery);
                                if ($upcomingResult && mysqli_num_rows($upcomingResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($upcomingResult)) {
                                        $statusClass = ($row['status'] === 'Approved') ? 'status-approved' : 'status-pending';
                                        echo '<tr class="' . $statusClass . '">';
                                        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['child_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['vaccine_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['appointment_date']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['hospital_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['time_slot']) . '</td>';
                                        echo '<td><span class="' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7">No upcoming vaccinations found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <?php
                require 'db.php';


                $status = $_GET['status'] ?? '';
                $vaccine = $_GET['vaccine'] ?? '';



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



                $sql .= " ORDER BY a.appointment_date DESC";
                $result = mysqli_query($conn, $sql);
                ?>
                <section class="content-section" id="vaccination-report">
                    <h1>Vaccination Report</h1>

                    <!-- Filter Form -->
                    <form class="filters" method="GET">

                        <select name="vaccine">
                            <option value="">All Vaccines</option>
                            <option value="Polio" <?= $vaccine == 'Polio' ? 'selected' : '' ?>>Polio</option>
                            <option value="BCG" <?= $vaccine == 'BCG' ? 'selected' : '' ?>>BCG</option>
                        </select>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $status == 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= $status == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                        <button type="submit">Filter</button>
                    </form>


                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Child Name</th>
                                <th>Parent Name</th>
                                <th>Vaccine</th>
                                <th>Disease</th>
                                <th>Hospital</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Status</th>
                                <th>Qty Used</th>
                            </tr>
                        </thead>
                        <tbody id="reportBody">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td data-label="ID"><?= $row['id'] ?></td>
                                        <td data-label="Child Name"><?= $row['child_name'] ?></td>
                                        <td data-label="Parent Name"><?= $row['parent_name'] ?></td>
                                        <td data-label="Vaccine"><?= $row['vaccine_name'] ?></td>
                                        <td data-label="Disease"><?= $row['disease_prevented'] ?></td>
                                        <td data-label="Hospital"><?= $row['hospital_name'] ?></td>
                                        <td data-label="Date"><?= $row['appointment_date'] ?></td>
                                        <td data-label="Time Slot"><?= $row['time_slot'] ?></td>
                                        <td data-label="Status"><?= $row['status'] ?></td>
                                        <td data-label="Qty Used">1</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>


                    <button onclick="window.print()">Print</button>

                </section>
                <!-- Upcoming Vaccine section end here -->

                <!-- for vaccine list section start here-->
                <div class="content-section" id="vaccine-list">
                    <h1>Vaccine List</h1>
                    <div class="vaccine-list-section-child">
                        <table class="vaccine-list-table-child">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Vaccine Name</th>
                                    <th>Disease Prevented</th>
                                    <th>Recommended Age</th>
                                    <th>Total Doses</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tbody>
                                <?php
                                include('db.php');
                                $query = "SELECT * FROM vaccines";
                                $result = mysqli_query($conn, $query);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['vaccine_name'] . "</td>";
                                    echo "<td>" . $row['disease_prevented'] . "</td>";
                                    echo "<td>" . $row['recommended_age'] . "</td>";
                                    echo "<td>" . $row['quantity'] . "</td>";
                                    echo "<td>Active</td>";
                                    echo "<td>
            <a href='edit_vaccine.php?id={$row['id']}'><button class='action-btn edit'>Edit</button></a>
            <a href='delete_vaccine.php?id={$row['id']}'><button class='action-btn delete'>Delete</button></a>
          </td>";
                                    echo "</tr>";
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- for vaccine list section end here-->

                <!-- hospital list section start here-->
                <div class="content-section" id="hospital-list">
                    <h1>Hospital List</h1>
                    <div class="hospital-list-content">

                        <table class="hospital-list-details">
                            <thead>
                                <tr>
                                    <th>Hosptal Name</th>
                                    <th>Location</th>
                                    <th>Contact Number</th>
                                    <th>Email Address</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="hospitalTableBody">

                            </tbody>

                        </table>
                    </div>
                </div>

<!-- hospital list section end here-->

<!-- Parent List section start from here -->
                <div class="content-section" id="parent-list">
                    <h1>Registered Parents</h1>
                    <?php
                    require 'db.php';

                    $sql = "SELECT name, phone, address FROM parents";
                    $result = $conn->query($sql);
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td data-label="Name"><?= htmlspecialchars($row['name']) ?></td>
                                    <td data-label="Phone"><?= htmlspecialchars($row['phone']) ?></td>
                                    <td data-label="Address"><?= htmlspecialchars($row['address']) ?></td>
                                </tr>
                        </tbody>
                    <?php } ?>

                    </table>
                </div>
                <!-- Parent List section start end here -->
                <!-- Pending Request Section  start here-->
                <div class="content-section" id="pending-request">
                    <h1>Pending Request</h1>
                    <div class="pending-request-section-child">
                        <table class="pending-request-table-child">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Child Name</th>
                                    <th>Vaccine Name</th>
                                    <th>Appointment Date</th>
                                    <th>Time Slot</th>
                                    <th>Hospital</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require 'db.php';

                                $sql = "SELECT a.id, c.name as child_name, v.vaccine_name, a.appointment_date, a.time_slot, h.hospital_name, a.status 
                    FROM appointments a
                    JOIN children c ON a.child_id = c.id
                    JOIN vaccines v ON a.vaccine_id = v.id
                    JOIN hospitals h ON a.hospital_id = h.id
                    WHERE a.status = 'Pending'";
                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['child_name']}</td>
                        <td>{$row['vaccine_name']}</td>
                        <td>{$row['appointment_date']}</td>
                        <td>{$row['time_slot']}</td>
                        <td>{$row['hospital_name']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <button class='action-btn view'>View</button>
                            <button class='action-btn approve'>Approve</button>
                            <button class='action-btn reject'>Reject</button>
                        </td>
                    </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No pending requests</td></tr>";
                                }

                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- pending request  section end here-->
                <!-- Add Vaccine Section start here-->
                <div class="content-section" id="add-vaccine">
                    <h1>Add a New Vaccine</h1>
                    <form class="add-vaccine-form" action="add_vaccine.php" method="POST">
                        <div class="form-group">
                            <label for="vaccine_name">Vaccine Name</label>
                            <input type="text" id="vaccine_name" name="vaccine_name" placeholder="e.g., Polio, MMR" required>
                        </div>
                        <div class="form-group">
                            <label for="disease_prevented">What Disease Does It Prevent?</label>
                            <input type="text" id="disease_prevented" name="disease_prevented" placeholder="e.g., Polio, Measles" required>
                        </div>
                        <div class="form-group">
                            <label for="recommended_age">Recommended Age Group</label>
                            <input type="text" id="recommended_age" name="recommended_age" placeholder="e.g., 6 months, 2-5 years" required>
                        </div>
                        <div class="form-group">
                            <label for="total_doses">How Many Doses?</label>
                            <input type="number" id="total_doses" name="total_doses" placeholder="e.g., 3" required>
                        </div>
                        <button type="submit" class="submit-btn" name="submit">Save Vaccine</button>
                    </form>
                </div>
                <!-- Add Vaccine Section end here-->
                <!-- Add Hospital Section start here -->
                <div class="content-section" id="add-hospital">
                    <h1>Add Hospital</h1>
                    <form class="add-hospital-form" action="add_hospital.php" method="POST">
                        <label for="hospitalName">Hospital Name:</label>
                        <input type="text" id="hospitalName" name="hospital_name" required>
                        <label for="location">Location / City:</label>
                        <input type="text" id="location" name="location" required>
                        <label for="contact">Contact Number:</label>
                        <input type="tel" id="contact" name="contact" required pattern="[0-9+() -]+" placeholder="+92-300-1234567">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" required placeholder="hospital@example.com">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" rows="3" required></textarea>
                        <button type="submit" class="submit-btn">Add Hospital</button>
                    </form>
                </div>
            </div>
<?php

require 'db.php';

$admin_id = $_SESSION['user_id'];
$admin_data = [];
if (isset($conn) && $admin_id) {
    $stmt = $conn->prepare("SELECT name, email, image FROM users WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
    } else {
        echo "<p style='color:red;'>Admin data not found for user_id: $admin_id</p>";
    }
    $stmt->close();
} else {
    echo "<p style='color:red;'>Database connection error or user_id missing.</p>";
}
?>

<div class="content-section" id="my-profile">
    <h1>My Profile</h1>
    <div class="profile-content">
        <?php
        // Always fetch admin data from users table, force admin_id = 43
        require 'db.php';
        $admin_id = 43;
        $admin_data = [];
        if ($admin_id && isset($conn)) {
            $stmt = $conn->prepare("SELECT name, email, image FROM users WHERE id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $admin_data = $result->fetch_assoc();
            }
            $stmt->close();
        }
        ?>
        <p><strong>Name:</strong> <?= htmlspecialchars($admin_data['name'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($admin_data['email'] ?? 'N/A') ?></p>
        <br><br>
        <form action="upload_admin_image.php" method="POST" enctype="multipart/form-data" id="image-form">
            <div class="profile-image-wrapper" onclick="document.getElementById('image-upload').click();">
                <img src="<?= htmlspecialchars($admin_data['image'] ?? 'default.jpg') ?>" alt="Profile Image">
            </div>
            <input type="file" id="image-upload" name="new_image" accept="image/*" onchange="document.getElementById('image-form').submit();">
        </form>
    </div>
</div>

        </div>
    </div>
    </div>

    <!-- Add Hospital Section end here-->
    <script src="main.js"></script>
    <script src="admin.js"></script>
</body>

</html>