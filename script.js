document.addEventListener('DOMContentLoaded', function () {

    // Show / Hide Password
    const showPasswordCheckbox = document.getElementById('showPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    if (showPasswordCheckbox && passwordInput) {
        showPasswordCheckbox.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
            if (confirmPasswordInput) {
                confirmPasswordInput.type = this.checked ? 'text' : 'password';
            }
        });
    }

    // Modal
    const modal = document.getElementById('errorModal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    if (modal && closeModalBtn) {

        // Close button
        closeModalBtn.addEventListener('click', function () {
            modal.classList.add('hidden');
        });

        // Close when clicking outside the modal content
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });

    }

});