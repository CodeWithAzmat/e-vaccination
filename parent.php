<?php
session_start();
require 'db.php';
$timeout_duration = 20000; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: index.html?timeout=1");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: index.html?login=required");
    exit;
}
$parent_id = $_SESSION['user_id'];
?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'child_added'): ?>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
    
      const toast = document.createElement("div");
      toast.textContent = "Child added successfully!";
      toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #4CAF50;
      color: white;
      padding: 15px 25px;
      border-radius: 8px;
      font-size: 16px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      opacity: 0;
      transform: translateY(-20px);
      transition: all 0.5s ease;
      z-index: 9999;
    `;
      document.body.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = "1";
        toast.style.transform = "translateY(0)";
      }, 100);


      setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateY(-20px)";
        setTimeout(() => toast.remove(), 500);
      }, 3000);


      const url = new URL(window.location.href);
      url.searchParams.delete('success');
      window.history.replaceState({}, document.title, url.pathname);
    });

    

setInterval(() => {
  fetch('keep_alive.php')
    .then(res => res.json())
    .then(data => {
      if (data.status !== 'alive') {
        console.warn('Session expired. Redirecting...');
        window.location.href = 'index.html';
      }
    })
    .catch(err => console.error('Session check failed', err));
}, 240000); 


  </script>
<?php endif; ?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parent Panel - E-Vaccination System</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="parent.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        </li>
        <li class="active" data-section="child-details">
          <a href="child-details">
            <i class="fas fa-child"></i>
            <span>My Children</span>
          </a>
        </li>
        <li data-section="upcoming-vaccination">
          <a href="upcoming-vaccination">
            <i class="fas fa-calendar-alt"></i>
            <span>Upcoming Vaccines</span>
          </a>
        </li>
        <li data-section="book-hospital">
          <a href="book-hospital">
            <i class="fas fa-hospital"></i>
            <span>Book Hospital</span>
          </a>
        </li>
        <li data-section="vaccination-report">
          <a href="vaccination-report">
            <i class="fas fa-file-alt"></i>
            <span>Vaccination Reports</span>
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

    <!-- Main Content -->
    <div class="main-content">
      <!-- Header -->
      <div class="header">
        <div class="header-left">
          <h2 id="pageTitle">Parent Dashboard</h2>
        </div>
        <div class="header-right">
          <div class="user-profile" id="userProfile">
            <?php
            require 'db.php';


            $parent_id = $_SESSION['user_id'] ?? null;
            $imagePath = "default.jpg"; 

            if ($parent_id) {
              $stmt = $conn->prepare("SELECT image FROM parents WHERE user_id = ?");
              $stmt->bind_param("i", $parent_id);
              $stmt->execute();
              $stmt->bind_result($image);
              if ($stmt->fetch() && $image) {
                $imagePath = "uploads/" . htmlspecialchars($image);
              }
              $stmt->close();
            }
            ?>

            <img src="<?php echo $imagePath; ?>" alt="User" style="width: 80px; height: 80px; object-fit:cover; border-radius: 50%;">
            <div class="dropdown-menu" id="userDropdown">
              <a><i class="fas fa-user"></i data-section="my-profile"> Profile</a>
              <a href="index.html" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="content">



        <div class="content-section" id="child-details">


          <div class="my-child">
            <div class="my-child-header">
              <h1>My Children</h1>
              <button class="add-child-btn" id="openChildFormBtn">Add Child</button>
            </div>
            <hr>

            <div id="childForm" class="child-form" style="display: none;">
              <h3>Add New Child</h3>
              <form action="add_child.php" method="POST" enctype="multipart/form-data">
                <label>Child Name:</label>
                <input type="text" name="child_name" required>

                <label>Age:</label>
                <input type="number" name="child_age" required>

                <label>Gender:</label>
                <select name="child_gender" required>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>

                <label>Date of Birth:</label>
                <input type="date" name="child_dob" required>

                <label>Child Photo:</label>
                <input type="file" name="child_photo" accept="image/*" required>

                <div class="form-actions">
                  <button type="submit" class="save-btn">Save Child</button>
                  <button type="button" id="cancelChildFormBtn" class="cancel-btn">Cancel</button>
                </div>
              </form>
            </div>

            <div class="dynamic-child" id="dynamic">
              <?php
              $stmt = $conn->prepare("SELECT * FROM children WHERE parent_id = ?");
              $stmt->bind_param("i", $parent_id);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                while ($child = $result->fetch_assoc()) {
                  echo '<div class="child-card">';
                  echo '<img src="' . htmlspecialchars($child['image_url']) . '" style="height:100px; width:100px; border-radius:50%;" alt="">';
                  echo '<h3>Child Name: ' . htmlspecialchars($child['name']) . '</h3>';
                  echo '<p>Age: ' . htmlspecialchars($child['age']) . '</p>';
                  echo '<p>Gender: ' . htmlspecialchars($child['gender']) . '</p>';
                  echo '<p>Date of Birth: ' . htmlspecialchars($child['dob']) . '</p>';

                  // Edit form
                  echo '<form action="edit_child.php" method="get" style="display:inline;">';
                  echo '<input type="hidden" name="child_id" value="' . htmlspecialchars($child['id']) . '">';
                  echo '<button type="submit" class="action-btn edit-child-btn">Edit</button>';
                  echo '</form>';

                  // Delete form
                  echo '<form action="delete_child.php" method="post" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this child?\');">';
                  echo '<input type="hidden" name="child_id" value="' . htmlspecialchars($child['id']) . '">';
                  echo '<button type="submit" class="action-btn delete-child-btn">Delete</button>';
                  echo '</form>';

                  echo '</div>';
                }
              } else {
                echo '<p>No children added yet.</p>';
              }

              $stmt->close();
              ?>

            </div>

          </div>
        </div>

        <!-- Upcoming Vaccine Sectio -->
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

                if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
                  echo '<tr><td colspan="7">Unauthorized access.</td></tr>';
                  exit;
                }

                $parent_id = $_SESSION['user_id'];
                $today = date('Y-m-d');

                $upcomingQuery = "SELECT a.id, c.name as child_name, v.vaccine_name, a.appointment_date, h.hospital_name, a.time_slot, a.status
    FROM appointments a
    JOIN children c ON a.child_id = c.id
    JOIN vaccines v ON a.vaccine_id = v.id
    JOIN hospitals h ON a.hospital_id = h.id
    WHERE c.parent_id = $parent_id 
    AND (a.status =  'Pending' OR a.status = 'Approved') 
    AND a.appointment_date >= '$today'
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
                    echo '<td>' . htmlspecialchars($row['status']) . '</td>';
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




        <!-- Book hospital section -->

        <div class="content-section" id="book-hospital">
          <!-- chatting button -->
          <button id="open-chat-btn">Ask Anything</button>

          <!-- chatting conatianor -->
          <div id="chat-container" style="display: none; flex-direction: column;">
            <button id="close-btn">×</button>
            <div id="chat-box" class="chat-box" style="height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;"></div>
            <input type="text" id="user-input" placeholder="Ask something..." />
            <button id="send-btn">Send</button>
          </div>


          <input type="hidden" id="child-id" value="<?= $_SESSION['child_id'] ?? '0' ?>">
          <form action="book_appointment.php" method="POST" id="bookingForm">
            <label>Select Child:</label>
            <select name="child_id">
              <?php
              $parent_id = $_SESSION['user_id'];
              $children = $conn->query("SELECT id, name FROM children WHERE parent_id = $parent_id");
              while ($row = $children->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
              }
              ?>
            </select>

            <label>Select Vaccine:</label>
            <select name="vaccine_id">
              <?php
              $vaccines = $conn->query("SELECT id, vaccine_name FROM vaccines");
              while ($row = $vaccines->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['vaccine_name']}</option>";
              }
              ?>
            </select>

            <label>Select Hospital:</label>
            <select name="hospital_id">
              <?php
              $hospitals = $conn->query("SELECT id, hospital_name FROM hospitals");
              while ($row = $hospitals->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['hospital_name']}</option>";
              }
              ?>
            </select>

            <label>Select Date:</label>
            <input type="date" name="appointment_date" required>

            <label>Select Time Slot:</label>
            <input type="text" name="time_slot" value="10:00 AM" required>

            <button type="submit">Book Appointment</button>
          </form>
        </div>


        <div class="content-section" id="vaccination-report">

          <div class="main-vaccination-report">
            <h1>Vaccination Report</h1>


            <div class="child-names" id="childNames">
              <?php
              $children = $conn->query("SELECT * FROM children WHERE parent_id = $parent_id");
              if ($children && $children->num_rows > 0) {
                while ($child = $children->fetch_assoc()) {
                  echo '<span class="child-name-select"'
                    . ' data-id="' . htmlspecialchars($child['id']) . '"'
                    . ' data-name="' . htmlspecialchars($child['name']) . '"'
                    . ' data-age="' . htmlspecialchars($child['age']) . '"'
                    . ' data-gender="' . htmlspecialchars($child['gender']) . '"'
                    . ' data-dob="' . htmlspecialchars($child['dob']) . '"'
                    . ' data-image="' . htmlspecialchars($child['image_url']) . '">'
                    . htmlspecialchars($child['name'])
                    . '</span>';
                }
              } else {
                echo '<span>No children found.</span>';
              }
              ?>

            </div>

            <hr>
            <h1>Vaccination Records</h1>
            <div class="vaccination-details" id="vaccinationDetails">
              <table>
                <thead>
                  <tr>
                    <th>Child Name</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Center</th>
                  </tr>
                </thead>
                <tbody id="vaccinationTableBody">
                  <?php
                  $records = $conn->query("SELECT c.id as child_id, c.name as child_name, a.status, a.appointment_date, h.hospital_name FROM appointments a JOIN children c ON a.child_id = c.id JOIN hospitals h ON a.hospital_id = h.id WHERE c.parent_id = $parent_id ORDER BY a.appointment_date DESC");
                  if ($records && $records->num_rows > 0) {
                    while ($row = $records->fetch_assoc()) {
                      echo '<tr data-child-id="' . htmlspecialchars($row['child_id']) . '">';
                      echo '<td>' . htmlspecialchars($row['child_name']) . '</td>';
                      echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                      echo '<td>' . htmlspecialchars($row['appointment_date']) . '</td>';
                      echo '<td>' . htmlspecialchars($row['hospital_name']) . '</td>';

                      echo '</tr>';
                    }
                  } else {
                    echo '<tr><td colspan="5">No vaccination records found.</td></tr>';
                  }
                  ?>
                </tbody>

              </table>

            </div>
          </div>
          <div style="margin-top: 20px; text-align:center;">
            <button onclick="printSelected()" class="print-btn"> Print Selected Child</button>
            <button onclick="printAll()" class="print-btn">Print All Children</button>
          </div>

        </div>

        <!-- my profile section start from here -->
        <?php

        

        if (!isset($_SESSION['parent_id'])) {
          echo "Unauthorized access.";
          exit;
        }

        $parent_id = $_SESSION['parent_id'];

        $sql = "SELECT name, phone, address FROM parents WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          $parent = $result->fetch_assoc();
        } else {
          echo "Parent not found.";
          exit;
        }
        ?>

        <div class="content-section" id="my-profile">
          <h1>Parent Dashboard</h1>
          <div class="parent-detail">
            <?php
          


            $parent_id = $_SESSION['user_id'] ?? null;
            $imagePath = 'uploads/default.jpg';

            if ($parent_id) {
              $stmt = $conn->prepare("SELECT image FROM parents WHERE user_id = ?");
              $stmt->bind_param("i", $parent_id);
              $stmt->execute();
              $stmt->bind_result($image);
              if ($stmt->fetch() && $image) {
                $imagePath = "uploads/" . $image;
              }
              $stmt->close();
            }
            ?>

            <!-- Parent Image -->
            <div style="text-align:center;">
              <form action="upload_parent_image.php" method="POST" enctype="multipart/form-data" id="imageForm">
                <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">

                <!-- Hidden  input -->
                <input type="file" name="parent_image" id="fileInput" accept="image/*" style="display:none;" onchange="document.getElementById('imageForm').submit();">

                <!-- Clickable  Image for updating -->
                <img class="parent-img" src="<?php echo $imagePath; ?>" alt="Click to Upload" style="cursor:pointer; width: 150px; height: 150px; object-fit:cover; border-radius: 50%;" onclick="document.getElementById('fileInput').click();">
              </form>
            </div>

            <div class="parent-details">
              <p><strong>Name :</strong> <?= htmlspecialchars($parent['name']) ?></p>
              <p><strong>Phone :</strong> <?= htmlspecialchars($parent['phone']) ?></p>
              <p><strong>Address:</strong> <?= htmlspecialchars($parent['address']) ?></p>
              <button onclick="document.getElementById('editForm').style.display='block'" class="action-btn edit">Edit</button>
            </div>
          </div>
        </div>

        <?php
        


        $parent_id = $_SESSION['user_id'] ?? null;
        $parent = ['name' => '', 'phone' => '', 'address' => '', 'image' => ''];

        if ($parent_id) {
          $stmt = $conn->prepare("SELECT name, phone, address, image FROM parents WHERE user_id = ?");
          $stmt->bind_param("i", $parent_id);
          $stmt->execute();
          $stmt->bind_result($name, $phone, $address, $image);
          if ($stmt->fetch()) {
            $parent['name'] = $name;
            $parent['phone'] = $phone;
            $parent['address'] = $address;
            $parent['image'] = $image;
          }
          $stmt->close();
        }
        ?>

        <!-- Hidden Edit Form -->
        <div id="editForm" style="display: none; margin-top: 20px;">
          <form action="update_parent_info.php" method="POST" enctype="multipart/form-data" class="parent-info-form">
            <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">

            <label><strong>Name:</strong></label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($parent['name']); ?>" required><br><br>

            <label><strong>Phone:</strong></label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($parent['phone']); ?>" required><br><br>

            <label><strong>Address:</strong></label>
            <textarea name="address" rows="3" required><?php echo htmlspecialchars($parent['address']); ?></textarea><br><br>

            <label><strong>Upload Image:</strong></label>
            <input type="file" name="parent_image" accept="image/*"><br><br>

            <?php if ($parent['image']): ?>
              <img src="uploads/<?php echo htmlspecialchars($parent['image']); ?>" alt="Profile" width="100" style="border-radius: 10px;"><br><br>
            <?php endif; ?>

            <button type="submit" class="action-btn save">Save Changes</button>
          </form>
        </div>




      </div>
    </div>
  </div>

  <script src="main.js"></script>


  <script src="parent.js"></script>

</body>

</html>