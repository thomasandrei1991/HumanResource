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

    // ==========================
    // EMPLOYEE FORM MANAGEMENT
    // ==========================

    const addEmployeeBtn = document.getElementById('addEmployeeBtn');
    const employeeFormPanel = document.querySelector('.employee-form-panel');
    const employeeSummary = document.querySelector('.employee-summary');
    const employeeDirectorySection = document.querySelector('.employee-directory-section');
    const panelHeader = document.querySelector('.panel-header');
    const dashboardShell = document.querySelector('.dashboard-shell');

    const addEmployeeForm = document.getElementById('addEmployeeForm');
    const editEmployeeForm = document.getElementById('editEmployeeForm');
    const cancelAddEmployeeBtn = document.getElementById('cancelAddEmployeeBtn');
    const cancelEditEmployeeBtn = document.getElementById('cancelEditEmployeeBtn');

    const isEditMode = new URLSearchParams(window.location.search).has('edit_id');
    const shouldShowFormOnLoad = document.body.dataset.showForm === '1';
    const shouldShowModalOnLoad = document.body.dataset.showModal === '1';

    function showEmployeeForm() {
        if (employeeSummary) employeeSummary.classList.add('hidden');
        if (employeeDirectorySection) employeeDirectorySection.classList.add('hidden');
        if (panelHeader) panelHeader.classList.add('hidden'); // Hides "Employee Directory" header
        
        if (employeeFormPanel) employeeFormPanel.classList.remove('hidden');
        if (dashboardShell) dashboardShell.classList.add('form-open');
    }

    function hideEmployeeForm() {
        if (employeeSummary) employeeSummary.classList.remove('hidden');
        if (employeeDirectorySection) employeeDirectorySection.classList.remove('hidden');
        if (panelHeader) panelHeader.classList.remove('hidden'); // Restores "Employee Directory" header
        
        if (employeeFormPanel) employeeFormPanel.classList.add('hidden');
        if (dashboardShell) dashboardShell.classList.remove('form-open');

        if (addEmployeeForm) addEmployeeForm.reset();
    }

    // Show employee form automatically on load if editing or on error
    if (isEditMode || shouldShowFormOnLoad) {
        showEmployeeForm();
    }

    // Show modal automatically on load
    if (shouldShowModalOnLoad && typeof showModal === 'function') {
        showModal(
            document.body.dataset.modalTitle || 'Duplicate Employee ID',
            document.body.dataset.modalMessage || 'Please complete the form.'
        );
    }

    // Event Listeners
    if (addEmployeeBtn) {
        addEmployeeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            showEmployeeForm();
        });
    }

    if (cancelAddEmployeeBtn) {
        cancelAddEmployeeBtn.addEventListener('click', function () {
            window.location.href = 'employee.php';
        });
    }

    if (cancelEditEmployeeBtn) {
        cancelEditEmployeeBtn.addEventListener('click', function () {
            window.location.href = 'employee.php';
        });
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
    // DELETE MODAL (GLOBAL)
    // Employee / Attendance / Department / Department Head
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

    deleteButtons.forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            deleteId = this.dataset.id;
            deleteType = this.dataset.type || "employee";

            const name = this.dataset.name || "this record";

            if (deleteMessage) {
                deleteMessage.textContent =
                    `Are you sure you want to delete ${name}?`;
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
        cancelDeleteBtn.addEventListener("click", function () {
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
        confirmDeleteBtn.addEventListener("click", function () {

            if (!deleteId) return;

            let url = "";

            switch (deleteType) {

                case "attendance":
                    url = `delete_attendance.php?id=${deleteId}`;
                    break;

                case "department":
                    url = `delete_department.php?id=${deleteId}`;
                    break;

                case "department_head":
                    url = `delete_department_head.php?id=${deleteId}`;
                    break;

                default:
                    url = `delete_employee.php?id=${deleteId}`;
                    break;
            }

            window.location.href = url;
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

// Employee Search Filtering
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveSearchInput');
    const searchBtn = document.getElementById('liveSearchBtn');
    const tableBody = document.getElementById('employeeTableBody');

    function performSearch() {
        if (!searchInput || !tableBody) return;

        const query = searchInput.value.trim();

        fetch(`search_employees.php?q=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                tableBody.innerHTML = '';

                if (!data.employees || data.employees.length === 0) {
                    const colSpan = data.isAdminOrHR ? 5 : 4;
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="${colSpan}" style="text-align: center;">
                                No employees found.
                            </td>
                        </tr>
                    `;
                    return;
                }

                data.employees.forEach(emp => {
                    const avatarInitials = ((emp.firstname ? emp.firstname.charAt(0) : '') + (emp.lastname ? emp.lastname.charAt(0) : '')).toUpperCase();

                    let actionButtons = '';
                    if (data.isAdminOrHR) {
                        actionButtons = `
                            <td class="action-buttons">
                                <button type="button" class="edit-btn" onclick="window.location.href='employee.php?edit_id=${emp.id}'">Edit</button>
                                <button type="button" class="employee-delete-btn delete-btn" data-id="${emp.id}" data-name="${emp.firstname} ${emp.lastname}" data-type="employee">Delete</button>
                            </td>
                        `;
                    }

                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="employee-name">
                                    <div class="emp-avatar blue-bg">${avatarInitials}</div>
                                    ${emp.firstname} ${emp.lastname}
                                </div>
                            </td>
                            <td>${emp.department || ''}</td>
                            <td>${emp.position || ''}</td>
                            <td>${emp.employment_status || ''}</td>
                            ${actionButtons}
                        </tr>
                    `;

                    tableBody.insertAdjacentHTML('beforeend', rowHTML);
                });
            })
            .catch(error => console.error('Error in search:', error));
    }

    // Gumagana sa bawat type at backspace
    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
    }

    // Gumagana rin kapag pinindot ang Search button
    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }
});


// Deaprtments Search Filtering
document.addEventListener('DOMContentLoaded', function () {
    const deptSearchInput = document.getElementById('liveSearchInput');
    const deptTableBody = document.getElementById('departmentTableBody');

    if (deptSearchInput && deptTableBody) {
        deptSearchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const rows = deptTableBody.querySelectorAll('tr');

            rows.forEach(row => {
                // Kunin ang text mula sa Department Name (Cell 0) at Department Head (Cell 1)
                const deptCell = row.cells[0];
                const headCell = row.cells[1];

                if (!deptCell || row.children.length === 1) return; // Laktawan ang "No departments found" row

                // Linisin ang extra spaces at gawing lowercase
                const deptText = deptCell.textContent.trim().toLowerCase();
                const headText = headCell ? headCell.textContent.trim().toLowerCase() : '';

                if (query === '') {
                    row.style.display = '';
                    return;
                }

                // Hatiin sa salita para ma-check kung may salitang NAGSISIMULA sa tina-type na letra
                const deptWords = deptText.split(/\s+/);
                const headWords = headText.split(/\s+/);

                const deptMatch = deptWords.some(word => word.startsWith(query));
                const headMatch = headWords.some(word => word.startsWith(query));

                // Ipapakita lamang ang row kung NAGSISIMULA sa query ang Department Name o Head
                if (deptMatch || headMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});