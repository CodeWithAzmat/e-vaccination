document.addEventListener('DOMContentLoaded', function () {
    // ✅ LOGIN FUNCTIONALITY
    const loginBtn = document.getElementById('loginBtn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function (e) {
            e.preventDefault();

            const role = document.getElementById('loginRole')?.value;
            const email = document.getElementById('loginEmail')?.value;
            const password = document.getElementById('loginPassword')?.value;

            if (!role || !email || !password) {
                alert('Please enter role, email, and password');
                return;
            }

            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ role, email, password })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.redirect) {
                    localStorage.setItem('currentUserRole', role);
                    localStorage.setItem('userEmail', email);
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Login failed');
                }
            })
            .catch(err => {
                console.error('Login error:', err);
                alert('Login request failed: ' + err.message);
            });
        });
    }

    // ✅ SIGNUP MODAL FUNCTIONALITY
    const registerLink = document.getElementById('registerLink');
    const registerModal = document.getElementById('registerModal');
    const closeRegisterModal = document.getElementById('closeRegisterModal');
    const cancelRegister = document.getElementById('cancelRegister');

    if (registerLink && registerModal) {
        registerLink.addEventListener('click', function (e) {
            e.preventDefault();
            registerModal.classList.add('show');
        });

        closeRegisterModal?.addEventListener('click', () => registerModal.classList.remove('show'));
        cancelRegister?.addEventListener('click', () => registerModal.classList.remove('show'));
    }

    // ✅ SHOW/HIDE EXTRA FIELDS BASED ON ROLE
    const registerRoleSelect = document.getElementById('registerRole');
    const parentFields = document.getElementById('parentFields');
    const hospitalFields = document.getElementById('hospitalFields');

    if (registerRoleSelect) {
        registerRoleSelect.addEventListener('change', function () {
            const role = this.value;
            parentFields.style.display = role === 'parent' ? 'block' : 'none';
            hospitalFields.style.display = role === 'hospital' ? 'block' : 'none';
        });

        registerRoleSelect.dispatchEvent(new Event('change')); // trigger once at load
    }

    // ✅ SUBMIT REGISTRATION
    const submitRegisterBtn = document.getElementById('submitRegister');
    if (submitRegisterBtn) {
        submitRegisterBtn.addEventListener('click', function (e) {
            e.preventDefault();

            const role = document.getElementById('registerRole')?.value;
            const name = document.getElementById('registerName')?.value;
            const email = document.getElementById('registerEmail')?.value;
            const password = document.getElementById('registerPassword')?.value;
            const confirmPassword = document.getElementById('registerConfirmPassword')?.value;

            if (!role || !name || !email || !password || !confirmPassword) {
                alert('Please fill all required fields');
                return;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            const formData = new FormData();
            formData.append('role', role);
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);

            if (role === 'parent') {
                const phone = document.getElementById('registerPhone')?.value;
                const address = document.getElementById('registerAddress')?.value;

                if (!phone || !address) {
                    alert('Please fill all parent-specific fields');
                    return;
                }

                formData.append('phone', phone);
                formData.append('address', address);
            } else if (role === 'hospital') {
                const location = document.getElementById('registerHospitalLocation')?.value;
                const contact = document.getElementById('registerHospitalContact')?.value;
                const address = document.getElementById('registerHospitalAddress')?.value;

                if (!location || !contact || !address) {
                    alert('Please fill all hospital-specific fields');
                    return;
                }

                formData.append('location', location);
                formData.append('contact', contact);
                formData.append('address', address);
            }

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                alert(data);
                if (data.toLowerCase().includes('success')) {
                    registerModal.classList.remove('show');
                }
            })
            .catch(err => {
                console.error('Registration error:', err);
                alert('Registration failed: ' + err.message);
            });
        });
    }
});
