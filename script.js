document.addEventListener('DOMContentLoaded', function () {

    // ==========================
    // Show / Hide Password
    // ==========================
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

    // ==========================
    // Error Modal
    // ==========================
    const modal = document.getElementById('errorModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const closeModalBtn = document.getElementById('closeModalBtn');

    function showModal(title, message) {
        if (modalTitle) {
            modalTitle.textContent = title || 'Employee Notice';
        }

        if (modalMessage) {
            modalMessage.textContent = message || 'Please complete the form.';
        }

        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function hideModal() {
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    if (modal && closeModalBtn) {

        closeModalBtn.addEventListener('click', function () {
            hideModal();
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                hideModal();
            }
        });

    }

    // ==========================
    // Employee Form
    // ==========================

    const addEmployeeBtn = document.getElementById('addEmployeeBtn');
    const employeeFormPanel = document.querySelector('.employee-form-panel');
    const employeeSummary = document.querySelector('.employee-summary');
    const employeePanel = document.querySelector('.employee-panel');
    const dashboardShell = document.querySelector('.dashboard-shell');

    const addEmployeeForm = document.getElementById('addEmployeeForm');
    const editEmployeeForm = document.getElementById('editEmployeeForm');

    const cancelAddEmployeeBtn = document.getElementById('cancelAddEmployeeBtn');
    const cancelEditEmployeeBtn = document.getElementById('cancelEditEmployeeBtn');

    const isEditMode = new URLSearchParams(window.location.search).has('edit_id');
    const shouldShowFormOnLoad = document.body.dataset.showForm === '1';
    const shouldShowModalOnLoad = document.body.dataset.showModal === '1';

    function showEmployeeForm() {

        if (employeeSummary)
            employeeSummary.classList.add('hidden');

        if (employeePanel)
            employeePanel.classList.add('hidden');

        if (employeeFormPanel)
            employeeFormPanel.classList.remove('hidden');

        if (dashboardShell)
            dashboardShell.classList.add('form-open');
    }

    function hideEmployeeForm() {

        if (employeeSummary)
            employeeSummary.classList.remove('hidden');

        if (employeePanel)
            employeePanel.classList.remove('hidden');

        if (employeeFormPanel)
            employeeFormPanel.classList.add('hidden');

        if (dashboardShell)
            dashboardShell.classList.remove('form-open');

        if (addEmployeeForm)
            addEmployeeForm.reset();

    }

    // Kapag Edit Mode o duplicate error, automatic ipakita ang form
    if (isEditMode || shouldShowFormOnLoad) {
        showEmployeeForm();
    }

    if (shouldShowModalOnLoad) {
        showModal(
            document.body.dataset.modalTitle || 'Duplicate Employee ID',
            document.body.dataset.modalMessage || 'Please complete the form.'
        );
    }

    // Add Employee Button
    if (addEmployeeBtn) {

        addEmployeeBtn.addEventListener('click', function (e) {

            e.preventDefault();

            showEmployeeForm();

        });

    }

    // Cancel Buttons
    if (cancelAddEmployeeBtn) {

        cancelAddEmployeeBtn.addEventListener('click', function () {

            window.location.href = "employee.php";

        });

    }

    if (cancelEditEmployeeBtn) {

        cancelEditEmployeeBtn.addEventListener('click', function () {

            window.location.href = "employee.php";

        });

    }


    const params = new URLSearchParams(window.location.search);

    const employeeModal = document.getElementById("employeeModal");
    const title = document.getElementById("employeeModalTitle");
    const message = document.getElementById("employeeModalMessage");
    const closeBtn = document.getElementById("closeEmployeeModal");

    if(employeeModal){

        if(params.get("error") === "duplicate"){

            title.textContent = "Employee Exists";
            message.textContent = "Employee ID already exists.";
            employeeModal.classList.remove("hidden");

        }

        if(params.get("success") === "added"){

            title.textContent = "Success";
            message.textContent = "Employee added successfully!";
            employeeModal.classList.remove("hidden");

        }

        closeBtn.addEventListener("click", function(){

            employeeModal.classList.add("hidden");

            window.history.replaceState({}, document.title, "employee.php");

        });

    }


    // ==========================
    // Attendance Form
    // ==========================

    const recordAttendanceBtn = document.getElementById("recordAttendanceBtn");

    const attendanceFormWrapper = document.querySelector(".attendance-form-wrapper");

    const attendanceSummary = document.querySelector(".attendance-container .employee-summary");

    const attendanceTable = document.querySelector(".attendance-container .employee-panel");

    function showAttendanceForm() {

        console.log(attendanceSummary);
        console.log(attendanceTable);
        console.log(attendanceFormWrapper);

        if (attendanceSummary) {
            attendanceSummary.classList.add("hidden");
        }

        if (attendanceTable) {
            attendanceTable.classList.add("hidden");
        }

        if (attendanceFormWrapper) {
            attendanceFormWrapper.classList.remove("hidden");
        }
    }

    function hideAttendanceForm(){

        if(attendanceSummary){
            attendanceSummary.classList.remove("hidden");
        }

        if(attendanceTable){
            attendanceTable.classList.remove("hidden");
        }

        if(attendanceFormWrapper){
            attendanceFormWrapper.classList.add("hidden");
        }

    }

    if(recordAttendanceBtn){

        recordAttendanceBtn.addEventListener("click",function(e){

            e.preventDefault();

            showAttendanceForm();

        });

    }

    const cancelAttendanceBtn=document.getElementById("cancelAttendanceBtn");

    if(cancelAttendanceBtn){

        cancelAttendanceBtn.addEventListener("click",function(){

            hideAttendanceForm();

        });

    }

    // Delete Modal
    const deleteButtons = document.querySelectorAll(".employee-delete-btn");
    const deleteModal = document.getElementById("deleteModal");
    const deleteMessage = document.getElementById("deleteMessage");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    let deleteEmployeeId = null;

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener("click", function(){
            if (deleteModal) {
                deleteModal.classList.add("hidden");
            }
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener("click", function(){
            if (deleteEmployeeId) {
                window.location.href = "delete_employee.php?id=" + deleteEmployeeId;
            }
        });
    }

    deleteButtons.forEach(function(button){

        button.addEventListener("click", function(e){
            e.preventDefault();

            deleteEmployeeId = this.dataset.id;

            if (deleteMessage) {
                deleteMessage.textContent =
                    "Are you sure you want to delete " +
                    this.dataset.name + "?";
            }

            if (deleteModal) {
                deleteModal.classList.remove("hidden");
            }
        });

    });

});