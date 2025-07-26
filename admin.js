document.addEventListener("DOMContentLoaded", function () {
    // Sidebar section toggle
    const menuItems = document.querySelectorAll(".sidebar-menu li[data-section]");
    const sections = document.querySelectorAll(".content-section");
    const pageTitle = document.getElementById('pageTitle');

    sections.forEach(section => {
        if (section.id === "dashboard-section") {
            section.style.display = "block";
            section.classList.add("active");
        } else {
            section.style.display = "none";
            section.classList.remove("active");
        }
    });

    function showSection(sectionId) {
        sections.forEach(section => {
            if (section.id === sectionId) {
                section.style.display = "block";
                section.classList.add("active");
            } else {
                section.style.display = "none";
                section.classList.remove("active");
            }
        });

        const activeMenuItem = document.querySelector(`.sidebar-menu li[data-section="${sectionId}"]`);
        if (activeMenuItem) {
            const label = activeMenuItem.querySelector("span");
            pageTitle.textContent = label ? label.textContent : "Dashboard";
        }
    }

    menuItems.forEach(item => {
        const link = item.querySelector("a");
        if (link) {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                menuItems.forEach(el => el.classList.remove("active"));
                item.classList.add("active");
                const sectionId = item.getAttribute("data-section");
                showSection(sectionId);
            });
        }
    });

    // Profile dropdown toggle
    const userProfile = document.getElementById("userProfile");
    const userDropdown = document.getElementById("userDropdown");

    if (userProfile && userDropdown) {
        userProfile.addEventListener("click", function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle("show");
        });

        document.addEventListener("click", function (event) {
            if (!userProfile.contains(event.target)) {
                userDropdown.classList.remove("show");
            }
        });
    }

    // Fetch hospital list when clicking hospital menu
    document.querySelector('[data-section="hospital-list"]').addEventListener('click', function () {
        fetch('fetch_hospitals.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('hospitalTableBody').innerHTML = data;
            });
    });

    // Appointment status update logic
    function updateStatus(appointmentId, status) {
        fetch('update_appointment_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: appointmentId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Appointment " + status + " successfully.");
                window.location.reload();
            } else {
                alert(data.message || "Error updating appointment.");
            }
        });
    }

    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            if (!row) return alert("Row not found!");

            const idCell = row.querySelector('td');
            if (!idCell) return alert("ID cell not found!");

            const appointmentId = idCell.textContent.trim();

            if (this.classList.contains('approve')) {
                updateStatus(appointmentId, 'Approved');
            } else if (this.classList.contains('reject')) {
                updateStatus(appointmentId, 'Rejected');
            } else if (this.classList.contains('view')) {
                alert("Viewing Appointment ID: " + appointmentId);
            }
        });
    });
});
