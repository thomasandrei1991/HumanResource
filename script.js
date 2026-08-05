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

    // Add Employee Form
    const addEmployeeBtn = document.getElementById('addEmployeeBtn');
    const addEmployeeFormPanel = document.getElementById('addEmployeeFormPanel');
    const employeeSummary = document.querySelector('.employee-summary');
    const employeePanel = document.querySelector('.employee-panel');
    const addEmployeeForm = document.getElementById('addEmployeeForm');

    function showAddEmployeeForm() {
        if (employeeSummary) employeeSummary.classList.add('hidden');
        if (employeePanel) employeePanel.classList.add('hidden');
        if (addEmployeeFormPanel) addEmployeeFormPanel.classList.remove('hidden');
    }

    function hideAddEmployeeForm() {
        if (employeeSummary) employeeSummary.classList.remove('hidden');
        if (employeePanel) employeePanel.classList.remove('hidden');
        if (addEmployeeFormPanel) addEmployeeFormPanel.classList.add('hidden');
    }

    if (addEmployeeBtn && addEmployeeFormPanel && employeeSummary && employeePanel) {
        addEmployeeBtn.addEventListener('click', function () {
            showAddEmployeeForm();
        });
    }

    const cancelAddEmployeeBtn = document.getElementById('cancelAddEmployeeBtn');

    if (addEmployeeForm) {
        addEmployeeForm.addEventListener('submit', function (event) {
            event.preventDefault();
            addEmployeeForm.reset();
            hideAddEmployeeForm();
        });
    }

    if (cancelAddEmployeeBtn) {
        cancelAddEmployeeBtn.addEventListener('click', function () {
            if (addEmployeeForm) addEmployeeForm.reset();
            hideAddEmployeeForm();
        });
    }

});