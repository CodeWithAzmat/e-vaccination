# E-Vaccination System

## Project Overview
The E-Vaccination System is a web application for managing child vaccination records, appointments, vaccine inventory, and hospital information. It streamlines the vaccination process for healthcare administrators, parents, and hospitals, providing real-time communication and robust record-keeping.

## User Roles & Panels

### Admin Panel
- **Role:** Healthcare administrator
- **Features:**
  - Dashboard: View vaccine stock, total patients, recent vaccinations
  - Manage children: View, edit, delete child records; see status (Pending/Approved/Rejected)
  - Manage parents: View registered parents and contact info
  - Manage vaccines: Add, edit, delete vaccines; track inventory
  - Manage hospitals: Add, edit, delete hospitals; view hospital list
  - Approve/reject appointments: Review pending requests and update status
  - Vaccination reports: Filter and print records by vaccine/status
  - Profile management: Update admin profile and image

### Parent Panel
- **Role:** Parent/guardian
- **Features:**
  - Register children: Add child profiles with photo, age, gender, DOB
  - Book appointments: Select child, vaccine, hospital, date, time slot
  - View upcoming vaccinations: See scheduled appointments for all children
  - Vaccination history: View completed vaccinations and print records
  - Print records: Print single or all vaccination records
  - Profile management: Update parent profile and image

### Hospital Panel
- **Role:** Hospital staff/manager
- **Features:**
  - Manage hospital profile: Update hospital details, contact info, address
  - View appointments: See upcoming and past appointments scheduled at the hospital
  - Confirm vaccinations: Mark appointments as completed/approved
  - View vaccine inventory: Track available vaccines at the hospital
  - Add/edit hospital information

## File Structure & Key Files
- `index.html` — Login and registration page
- `admin.php` — Admin dashboard and management
- `parent.php` — Parent dashboard and child management
- `hospital.php` — Hospital dashboard and appointment management
- `db.php` — Database connection
- `add_child.php`, `add_vaccine.php`, `add_hospital.php` — Data entry scripts
- `main.js`, `admin.js`, `hospital.js`, `parent.js` — Frontend scripts for UI interactivity and section switching
- `style.css`, `admin.css`, `hospital.css`, `parent.css` — Stylesheets for each panel
- `uploads/` — Directory for profile and child images

## Database Tables
- `users` — Stores login credentials and roles
- `parents` — Parent details (id, name, phone, address, email, image)
- `children` — Child details (id, name, age, dob, gender, image_url, parent_id)
- `vaccines` — Vaccine inventory (id, vaccine_name, disease_prevented, recommended_age, quantity)
- `hospitals` — Hospital details (id, hospital_name, location, contact, email, address)
- `appointments` — Vaccination appointments (id, child_id, parent_id, vaccine_id, hospital_id, appointment_date, time_slot, status)
- `vaccination_history` — Completed vaccinations (id, child_id, vaccine_id, date_given, hospital_id, doctor_name)

## Main Workflows

### Admin Workflow
1. Log in as admin.
2. View dashboard for system overview.
3. Manage children, parents, vaccines, and hospitals.
4. Approve or reject pending vaccination requests.
5. View and print vaccination reports.
6. Update profile and image.

### Parent Workflow
1. Register or log in as parent.
2. Add children with complete details and photo.
3. Book vaccination appointments for children.
4. View upcoming vaccinations and history.
5. Print single or all vaccination records.
6. Update profile information and image.

### Hospital Workflow
1. Log in as hospital staff/manager.
2. Update hospital profile and contact info.
3. View appointments scheduled at the hospital.
4. Confirm/mark vaccinations as completed.
5. Track vaccine inventory.
6. Add/edit hospital information.

## Technical Stack
- PHP (server-side logic)
- MySQL/MariaDB (database)
- HTML, CSS, JavaScript (frontend)
- FontAwesome, Chart.js (UI enhancements)

## Integration Notes
- Session management and role-based access are implemented in all main PHP files.
- Section switching and dashboard navigation are handled by dedicated JS files for each panel.
- Image uploads are managed via the `uploads/` directory and related PHP scripts.
- Database connection is centralized in `db.php`.

## Troubleshooting
- **Foreign Key Errors:** Ensure parent records exist before adding children.
- **Image Upload Issues:** Check that the `uploads/` directory is writable and file types are allowed.
- **Login/Signup Issues:** Verify database connection and required tables exist.
- **Data Not Saving:** Ensure form actions and method attributes are set correctly in HTML and PHP scripts.

## Customization
- Add more vaccine types, hospital details, or child fields as needed.
- Update stylesheets for branding.
- Add more filters or export options to the reports section.

## License & Credits
This project is for educational/demo purposes. Please adapt, secure, and test thoroughly before production use.

---

## File-by-File Project Report

Below is a numbered summary of each file and its purpose for your presentation:

1. **add_child.php** — Adds a new child for a parent, including validation and image upload.
2. **add_hospital.php** — Admin adds a new hospital with all details.
3. **add_vaccine.php** — Admin/hospital adds a new vaccine, checks for duplicates, manages quantity.
4. **add_vaccine_hospital.php** — Hospital adds or updates vaccine inventory.
5. **admin.php** — Admin dashboard: manage children, parents, vaccines, hospitals, appointments, reports.
6. **admin_approve_appointment.php** — Approves appointments and updates vaccine stock.
7. **admin_vaccine_report.php** — Shows vaccine inventory for all hospitals.
8. **approve_hospital.php** — Admin approves/rejects hospital registrations.
9. **book_appointment.php** — Parent books vaccination appointments for children.
10. **chatbot.php** — Parent chatbot for vaccine info and FAQs.
11. **create_user.php** — Script to create a new user (parent) with password hashing.
12. **db.php** — Central database connection for all PHP scripts.
13. **delete_child.php** — Parent deletes a child record.
14. **delete_vaccine.php** — Admin deletes a vaccine record.
15. **edit_child.php** — Parent edits child details.
16. **edit_vaccine.php** — Admin edits vaccine details.
17. **fetch_hospitals.php** — Lists all hospitals in the system.
18. **fetch_hospitals_and_vaccines.php** — Returns hospitals and available vaccines for booking.
19. **fetch_pending_requests.php** — Gets all pending appointment requests for admin.
20. **get_children.php** — Returns all children for a parent (API/AJAX).
21. **handle_appointment_status.php** — Updates appointment status and vaccine stock.
22. **hospital.php** — Hospital dashboard: profile, appointments, vaccine stock, info.
23. **index.html** — Login and registration page for all users.
24. **login.php** — Handles login for all roles, sets session variables.
25. **main.js** — Frontend JS for login and UI interactivity.
26. **parent.php** — Parent dashboard: children, appointments, history, profile.
27. **register.php** — Handles user registration for parents/hospitals.
28. **style.css** — Main stylesheet for layout and design.
29. **update_appointment_status.php** — Updates appointment status and vaccine stock.
30. **update_hospital_info.php** — Hospital updates profile info.
31. **update_or_insert_vaccine.php** — Hospital updates/adds vaccine inventory.
32. **update_parent_info.php** — Parent updates profile info and image.
33. **upload_admin_image.php** — Admin profile image upload.
34. **upload_hospital_image.php** — Hospital profile image upload.
35. **upload_parent_image.php** — Parent profile image upload.
36. **vaccine_report.php** — Detailed report of vaccination appointments, filterable by status/vaccine/search.
---

## Role-Based File Mapping

Below is a mapping of which files are primarily used by each user role in the system:

### Admin Files
- admin.php
- add_hospital.php
- add_vaccine.php
- admin_approve_appointment.php
- admin_vaccine_report.php
- approve_hospital.php
- delete_vaccine.php
- edit_vaccine.php
- fetch_pending_requests.php
- vaccine_report.php
- upload_admin_image.php

### Parent Files
- parent.php
- add_child.php
- book_appointment.php
- chatbot.php
- create_user.php
- delete_child.php
- edit_child.php
- get_children.php
- update_parent_info.php
- upload_parent_image.php

### Hospital Files
- hospital.php
- add_vaccine_hospital.php
- update_hospital_info.php
- update_or_insert_vaccine.php
- upload_hospital_image.php

### Shared/General Files
- db.php
- fetch_hospitals.php
- fetch_hospitals_and_vaccines.php
- handle_appointment_status.php
- index.html
- login.php
- main.js
- register.php
- style.css

> This mapping helps clarify which files are relevant for each user role and can be used directly in your presentation.
