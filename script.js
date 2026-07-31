// Login Code
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const modal = document.getElementById('errorModal');
    const modalMessage = document.getElementById('modalMessage');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const showPasswordCheckbox = document.getElementById('showPassword');
    const passwordInput = document.getElementById('password');

    if (showPasswordCheckbox && passwordInput) {
        showPasswordCheckbox.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    }

    if (loginForm && modal && modalMessage && closeModalBtn) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = passwordInput ? passwordInput.value.trim() : '';

            if (username === 'admin' && password === '12345') {
                window.location.href = 'dashboard.php';
                return;
            }

            modalMessage.textContent = 'Incorrect username or password.';
            modal.classList.remove('hidden');
        });

        closeModalBtn.addEventListener('click', function () {
            modal.classList.add('hidden');
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }

});

// Dashboard code


