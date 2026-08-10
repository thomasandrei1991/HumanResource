document.addEventListener('DOMContentLoaded', function () {

    // ==========================
    // SHOW / HIDE PASSWORD
    // ==========================

    const showPasswordCheckbox = document.getElementById('showPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    if (showPasswordCheckbox && passwordInput) {

        showPasswordCheckbox.addEventListener('change', function () {

            passwordInput.type = this.checked ? 'text' : 'password';

            if (confirmPasswordInput) {
                confirmPasswordInput.type =
                    this.checked ? 'text' : 'password';
            }

        });

    }


    // ==========================
    // ERROR MODAL
    // ==========================

    const modal = document.getElementById('errorModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const closeModalBtn = document.getElementById('closeModalBtn');

    function showModal(title, message) {

        if (modalTitle) {
            modalTitle.textContent =
                title || 'Employee Notice';
        }

        if (modalMessage) {
            modalMessage.textContent =
                message || 'Please complete the form.';
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
    // EMPLOYEE FORM
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
        if (employeeSummary) {
            employeeSummary.classList.add('hidden');
        }
        if (employeePanel) {
            employeePanel.classList.add('hidden');
        }
        if (employeeFormPanel) {
            employeeFormPanel.classList.remove('hidden');
        }
        if (dashboardShell) {
            dashboardShell.classList.add('form-open');
        }
    }


    function hideEmployeeForm() {
        if (employeeSummary) {
            employeeSummary.classList.remove('hidden');
        }
        if (employeePanel) {
            employeePanel.classList.remove('hidden');
        }
        if (employeeFormPanel) {
            employeeFormPanel.classList.add('hidden');
        }
        if (dashboardShell) {
            dashboardShell.classList.remove('form-open');
        }
        if (addEmployeeForm) {
            addEmployeeForm.reset();
        }
    }

    // Show employee form automatically
    if (isEditMode || shouldShowFormOnLoad) {
        showEmployeeForm();
    }

    // Show modal automatically
    if (shouldShowModalOnLoad) {
        showModal(
            document.body.dataset.modalTitle || 'Duplicate Employee ID', document.body.dataset.modalMessage || 'Please complete the form.'
        );
    }

    // ADD EMPLOYEE BUTTON
    if (addEmployeeBtn) {
        addEmployeeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            showEmployeeForm();
        });
    }

    // CANCEL ADD
    if (cancelAddEmployeeBtn) {
        cancelAddEmployeeBtn.addEventListener('click', function () {
            window.location.href = 'employee.php';
        });
    }

    // CANCEL EDIT
    if (cancelEditEmployeeBtn) {
        cancelEditEmployeeBtn.addEventListener('click', function () {
            window.location.href = 'employee.php';
        });
    }

    // ==========================
    // EMPLOYEE SUCCESS / ERROR
    // MODAL
    // ==========================

    const params = new URLSearchParams(window.location.search);
    const employeeModal = document.getElementById('employeeModal');
    const employeeModalTitle = document.getElementById('employeeModalTitle');
    const employeeModalMessage = document.getElementById('employeeModalMessage');
    const closeEmployeeModal = document.getElementById('closeEmployeeModal');

    if (employeeModal) {
        if (params.get('error') === 'duplicate') {
            if (employeeModalTitle) {
                employeeModalTitle.textContent = 'Employee Exists';
            }
            if (employeeModalMessage) {
                employeeModalMessage.textContent = 'Employee ID already exists.';
            }
            employeeModal.classList.remove('hidden');
        }
        if (params.get('success') === 'added') {
            if (employeeModalTitle) {
                employeeModalTitle.textContent = 'Success';
            }
            if (employeeModalMessage) {
                employeeModalMessage.textContent = 'Employee added successfully!';
            }
            employeeModal.classList.remove('hidden');
        }

        if (closeEmployeeModal) {
            closeEmployeeModal.addEventListener('click', function () {
                employeeModal.classList.add('hidden');
                window.history.replaceState({}, document.title, 'employee.php');

            });
        }
    }

    // ==========================
    // ATTENDANCE FORM
    // ==========================

    const recordAttendanceBtn = document.getElementById('recordAttendanceBtn');
    const attendanceFormWrapper = document.querySelector('.attendance-form-wrapper');
    const attendanceSummary = document.querySelector('.attendance-container .employee-summary');
    const attendanceTable = document.querySelector('.attendance-container .employee-panel');

    function showAttendanceForm() {
        if (attendanceSummary) {
            attendanceSummary.classList.add('hidden');
        }
        if (attendanceTable) {
            attendanceTable.classList.add('hidden');
        }
        if (attendanceFormWrapper) {
            attendanceFormWrapper.classList.remove('hidden');
        }
    }

    function hideAttendanceForm() {
        if (attendanceSummary) {
            attendanceSummary.classList.remove('hidden');
        }
        if (attendanceTable) {
            attendanceTable.classList.remove('hidden');
        }
        if (attendanceFormWrapper) {
            attendanceFormWrapper.classList.add('hidden');
        }
    }


    // RECORD ATTENDANCE
    if (recordAttendanceBtn) {
        recordAttendanceBtn.addEventListener('click', function (e) {
            e.preventDefault();
            showAttendanceForm();
        });
    }


    // CANCEL ATTENDANCE
    const cancelAttendanceBtn = document.getElementById('cancelAttendanceBtn');

    if (cancelAttendanceBtn) {
        cancelAttendanceBtn.addEventListener('click', function () {
            hideAttendanceForm();
        });
    }

    // ==========================
    // DELETE MODAL
    // ==========================

    const deleteButtons = document.querySelectorAll(".employee-delete-btn");
    const deleteModal = document.getElementById("deleteModal");
    const deleteMessage = document.getElementById("deleteMessage");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
    let deleteId = null;
    let deleteType = null;

    // ==========================
    // OPEN DELETE MODAL
    // ==========================

    deleteButtons.forEach(function(button) {
        button.addEventListener("click", function(event) {
            event.preventDefault();
            // Kunin ang ID mismo sa data-id
            deleteId = this.dataset.id;
            // Employee or Attendance
            deleteType = this.dataset.type || "employee";
            const name = this.dataset.name;
            console.log("DELETE ID:", deleteId);
            console.log("DELETE TYPE:", deleteType);

            if (deleteMessage) {
                deleteMessage.textContent = "Are you sure you want to delete " + name + "?";
            }

            if (deleteModal) {
                deleteModal.classList.remove("hidden");
            }

        });

    });


    // ==========================
    // CANCEL DELETE
    // ==========================

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener("click", function() {
            deleteId = null;
            deleteType = null;
            if (deleteModal) {
                deleteModal.classList.add("hidden");
            }

        });

    }

    // ==========================
    // CONFIRM DELETE
    // ==========================

    if (confirmDeleteBtn) {

        confirmDeleteBtn.addEventListener("click", function() {

            console.log("CONFIRM DELETE");
            console.log("ID:", deleteId);
            console.log("TYPE:", deleteType);

            if (!deleteId) {
                console.log("No delete ID found.");
                return;
            }

            // ATTENDANCE DELETE
            if (deleteType === "attendance") {

                window.location.href =
                    "delete_attendance.php?id=" +
                    encodeURIComponent(deleteId);

            }

            // DEPARTMENT DELETE
            else if (deleteType === "department") {

                window.location.href =
                    "delete_department.php?id=" +
                    encodeURIComponent(deleteId);

            }

            // EMPLOYEE DELETE
            else {

                window.location.href =
                    "delete_employee.php?id=" +
                    encodeURIComponent(deleteId);

            }

        });

    }


    // ==========================
    // DEPARTMENT FORM
    // ==========================

    const addDepartmentBtn = document.getElementById('addDepartmentBtn');
    const addDepartmentFormPanel = document.getElementById('addDepartmentFormPanel');
    const cancelAddDepartmentBtn = document.getElementById('cancelAddDepartmentBtn');

    const departmentSummary = document.querySelector('.department-container .employee-summary');
    const departmentPanel = document.querySelector('.department-container .employee-panel');


    // SHOW DEPARTMENT FORM

    if (addDepartmentBtn) {

        addDepartmentBtn.addEventListener('click', function () {

            if (departmentSummary) {
                departmentSummary.classList.add('hidden');
            }

            if (departmentPanel) {
                departmentPanel.classList.add('hidden');
            }

            if (addDepartmentFormPanel) {
                addDepartmentFormPanel.classList.remove('hidden');
            }

        });

    }


    // CANCEL DEPARTMENT FORM

    if (cancelAddDepartmentBtn) {

        cancelAddDepartmentBtn.addEventListener('click', function () {

            if (departmentSummary) {
                departmentSummary.classList.remove('hidden');
            }

            if (departmentPanel) {
                departmentPanel.classList.remove('hidden');
            }

            if (addDepartmentFormPanel) {
                addDepartmentFormPanel.classList.add('hidden');
            }

        });

    }

    // ==========================
    // LEAVE FORM — DATE CALCULATION
    // ==========================

    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const totalDays = document.getElementById('totalDays');

    function calculateLeaveDays() {

        if (!startDate || !endDate || !totalDays) {
            return;
        }

        if (!startDate.value || !endDate.value) {
            totalDays.value = '';
            return;
        }

        const start = new Date(startDate.value);
        const end = new Date(endDate.value);

        if (end < start) {
            totalDays.value = '';
            return;
        }

        const difference = end.getTime() - start.getTime();
        const days = Math.floor(difference / (1000 * 60 * 60 * 24)) + 1;

        totalDays.value = days;
    }

    if (startDate) {
        startDate.addEventListener('change', calculateLeaveDays);
    }

    if (endDate) {
        endDate.addEventListener('change', calculateLeaveDays);
    }


    // ==========================
    // SHOW / HIDE LEAVE FORM
    // ==========================

    const newLeaveBtn = document.getElementById('newLeaveBtn');
    const leaveFormPanel = document.getElementById('leaveFormPanel');
    const cancelLeaveBtn = document.getElementById('cancelLeaveBtn');
    const leaveForm = document.getElementById('leaveForm');

    const leaveSummary = document.querySelector(
        '.leave-container .employee-summary'
    );

    const leavePanel = document.querySelector(
        '.leave-container .employee-panel'
    );

    function showLeaveForm() {
        if (leaveSummary) leaveSummary.classList.add('hidden');
        if (leavePanel) leavePanel.classList.add('hidden');
        if (leaveFormPanel) leaveFormPanel.classList.remove('hidden');
    }

    function hideLeaveForm() {
        if (leaveSummary) leaveSummary.classList.remove('hidden');
        if (leavePanel) leavePanel.classList.remove('hidden');
        if (leaveFormPanel) leaveFormPanel.classList.add('hidden');
        if (leaveForm) leaveForm.reset();
    }

    if (newLeaveBtn) {
        newLeaveBtn.addEventListener('click', function (event) {
            event.preventDefault();
            showLeaveForm();
        });
    }

    if (cancelLeaveBtn) {
        cancelLeaveBtn.addEventListener('click', function () {
            hideLeaveForm();
        });
    }


});