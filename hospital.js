  document.addEventListener("DOMContentLoaded", function() {


            const menuItems = document.querySelectorAll(".sidebar-menu li[data-section]");
            const sections = document.querySelectorAll(".content-section");
            const pageTitle = document.getElementById('pageTitle');

            // Hide all sections except dashboard-section on load
            sections.forEach(section => {
                if (section.id === "hospital-dashboard") {
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
                // Update the page title
                const activeMenuItem = document.querySelector(`.sidebar-menu li[data-section="${sectionId}"]`);
                if (activeMenuItem) {
                    const label = activeMenuItem.querySelector("span");
                    pageTitle.textContent = label ? label.textContent : "Dashboard";
                }
            }

            // Attach click listener to <a> inside each li
            menuItems.forEach(item => {
                const link = item.querySelector("a");
                if (link) {
                    link.addEventListener("click", function(e) {
                        e.preventDefault(); // Prevent default anchor behavior
                        menuItems.forEach(el => el.classList.remove("active"));
                        item.classList.add("active");
                        const sectionId = item.getAttribute("data-section");
                        showSection(sectionId);
                    });
                }
            });

            // Dropdown toggle for user profile
            const userProfile = document.getElementById("userProfile");
            const userDropdown = document.getElementById("userDropdown");

            if (userProfile && userDropdown) {
                userProfile.addEventListener("click", function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle("show");
                });

                document.addEventListener("click", function(event) {
                    if (!userProfile.contains(event.target)) {
                        userDropdown.classList.remove("show");
                    }
                });
            }
            
        });

        