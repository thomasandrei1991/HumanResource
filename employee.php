<?php
    session_start();
    require_once 'database.php';

    // ==========================================================
    // LOGIN CHECK
    // ==========================================================
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // ==========================================================
    // CURRENT USER & ROLE CHECK
    // ==========================================================
    $userId = intval($_SESSION['user_id']);
    $userRole = $_SESSION['role'] ?? '';
    $employeeId = intval($_SESSION['employee_id'] ?? 0);

    $isAdminOrHR = ($userRole === 'Admin' || $userRole === 'HR');
    $isDepartmentHead = ($userRole === 'Department Head');

    if (!$isAdminOrHR && !$isDepartmentHead) {
        header("Location: dashboard.php");
        exit();
    }

    // ==========================================================
    // GET DEPARTMENT HEAD'S DEPARTMENT
    // ==========================================================
    $headDepartment = '';

    if ($isDepartmentHead) {
        $headName = $_SESSION['fullname'] ?? '';
        $departmentQuery = mysqli_prepare($conn, "SELECT department_name
            FROM departments
            WHERE department_head = ?
            AND status = 'active'
            LIMIT 1"
        );

        mysqli_stmt_bind_param($departmentQuery, "s", $headName);
        mysqli_stmt_execute($departmentQuery);
        $departmentResult = mysqli_stmt_get_result($departmentQuery);

        if (mysqli_num_rows($departmentResult) > 0) {
            $departmentData = mysqli_fetch_assoc($departmentResult);
            $headDepartment = $departmentData['department_name'] ?? '';
        }
    }

    // ==========================================================
    // GET EMPLOYEES WITH SEARCH FILTER (INITIAL PAGE LOAD)
    // ==========================================================
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $searchParam = $searchTerm . '%';

    if ($isAdminOrHR) {
        if (!empty($searchTerm)) {
            $employeesQuery = mysqli_prepare($conn, "SELECT id, employee_id, firstname, lastname, email, phone, 
                department, position, date_hired, salary, employment_status
                FROM employees 
                WHERE firstname LIKE ? 
                OR lastname LIKE ? 
                OR department LIKE ? 
                OR position LIKE ? 
                OR employee_id LIKE ?
                ORDER BY lastname ASC"
            );
            mysqli_stmt_bind_param($employeesQuery, "sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
        } else {
            $employeesQuery = mysqli_prepare($conn, "SELECT id, employee_id, firstname, lastname, email, phone, 
                department, position, date_hired, salary, employment_status
                FROM employees 
                ORDER BY lastname ASC"
            );
        }
    } else {
        if (!empty($searchTerm)) {
            $employeesQuery = mysqli_prepare($conn, "SELECT id, employee_id, firstname, lastname, email, phone,
                    department, position, date_hired, salary, employment_status
                FROM employees 
                WHERE LOWER(department) = LOWER(?)
                AND (firstname LIKE ? OR lastname LIKE ? OR position LIKE ? OR employee_id LIKE ?)
                ORDER BY lastname ASC"
            );
            mysqli_stmt_bind_param($employeesQuery, "sssss", $headDepartment, $searchParam, $searchParam, $searchParam, $searchParam);
        } else {
            $employeesQuery = mysqli_prepare($conn, "SELECT id, employee_id, firstname, lastname, email, phone,
                    department, position, date_hired, salary, employment_status
                FROM employees 
                WHERE LOWER(department) = LOWER(?)
                ORDER BY lastname ASC"
            );
            mysqli_stmt_bind_param($employeesQuery, "s", $headDepartment);
        }
    }

    mysqli_stmt_execute($employeesQuery);
    $employeesResult = mysqli_stmt_get_result($employeesQuery);

    // ==========================================================
    // EMPLOYEE SUMMARY COUNTS
    // ==========================================================
    if ($isAdminOrHR) {
        $totalEmployees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees"))['total'] ?? 0;
        $activeEmployees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Active'"))['total'] ?? 0;
        $onLeaveEmployees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'On Leave'"))['total'] ?? 0;
    } else {
        $countQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total,
                SUM(employment_status = 'Active') AS active,
                SUM(employment_status = 'On Leave') AS on_leave
            FROM employees
            WHERE LOWER(department) = LOWER(?)"
        );

        mysqli_stmt_bind_param($countQuery, "s", $headDepartment);
        mysqli_stmt_execute($countQuery);
        $countResult = mysqli_stmt_get_result($countQuery);
        $countData = mysqli_fetch_assoc($countResult);
        $totalEmployees = $countData['total'] ?? 0;
        $activeEmployees = $countData['active'] ?? 0;
        $onLeaveEmployees = $countData['on_leave'] ?? 0;
    }

    // Kunin ang search query kung mayroon
    // Kunin ang search query kung mayroon
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    if (!empty($search)) {
        $searchTerm = "%{$search}%";
        
        // Ginamitan ng CONCAT(firstname, ' ', lastname) para mahanap ang buong pangalan
        $stmt = mysqli_prepare(
            $conn, 
            "SELECT id, employee_id, firstname, lastname, department, position, employment_status 
            FROM employees 
            WHERE CONCAT(firstname, ' ', lastname) LIKE ? 
                OR CONCAT(lastname, ' ', firstname) LIKE ?
                OR employee_id LIKE ? 
                OR email LIKE ?
            ORDER BY id DESC"
        );

        // 4 na parameters para sa 4 na '?' na nasa query
        mysqli_stmt_bind_param($stmt, "ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);
        $employeesResult = mysqli_stmt_get_result($stmt);
    } else {
        // Kapag walang sinearch, ipalabas lahat
        $employeesResult = mysqli_query(
            $conn, 
            "SELECT id, employee_id, firstname, lastname, department, position, employment_status 
            FROM employees 
            ORDER BY id DESC"
        );
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/employee.css">
    <title>Employees | HR Dashboard</title>
</head>

<body
    class="dashboard-page"
    data-show-form="<?php echo isset($_SESSION['employee_error']) ? '1' : '0'; ?>"
    data-show-modal="<?php echo isset($_SESSION['employee_error']) ? '1' : '0'; ?>"
    data-modal-title="Duplicate Employee ID"
    data-modal-message="<?php echo isset($_SESSION['employee_error']) ? htmlspecialchars($_SESSION['employee_error'], ENT_QUOTES, 'UTF-8') : ''; ?>"
>

<div class="dashboard-shell">
    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="employee-container">

            <!-- PAGE HEADER & SEARCH -->
            <div class="page-header">
                <div>
                    <p class="page-kicker">People Management</p>
                    <h1>Employees</h1>
                </div>

                <div class="table-tools">
                    <div class="employee-search-form">
                        <input class="search-input" id="searchInput" id="liveSearchInput" type="text" placeholder="Search employee..." autocomplete="off">
                        <button class="search-btn" id="liveSearchBtn" type="button">Search</button>
                    </div>
                </div>

                <?php if ($isAdminOrHR): ?>
                    <button type="button" class="primary-btn" id="addEmployeeBtn">
                        + Add Employee
                    </button>
                <?php endif; ?>
            </div>

            <!-- SUMMARY CARDS -->
            <div class="employee-summary">
                <div class="summary-card blue">
                    <h3>Total Staff</h3>
                    <p><?php echo $totalEmployees; ?></p>
                </div>
                <div class="summary-card green">
                    <h3>Active</h3>
                    <p><?php echo $activeEmployees; ?></p>
                </div>
                <div class="summary-card purple">
                    <h3>On Leave</h3>
                    <p><?php echo $onLeaveEmployees; ?></p>
                </div>
            </div>

            <!-- ADD / EDIT FORM -->
            <?php
                $error = $_GET['error'] ?? '';
                $success = $_GET['success'] ?? '';
                $isEditMode = isset($_GET['edit_id']) && !empty($_GET['edit_id']);
            ?>

            <?php if ($isAdminOrHR): ?>
                <?php if ($isEditMode): ?>
                    <?php include 'edit_employee.php'; ?>
                <?php else: ?>
                    <?php include 'add_employee.php'; ?>
                <?php endif; ?>
            <?php endif; ?>

            <!-- EMPLOYEE TABLE SECTION -->
            <div class="employee-directory-section"> 
                <div class="panel-header">
                    <h3>Employee Directory</h3>
                </div> 
                
                <div class="employee-panel">

                    <table class="dashboard-table employee-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Status</th>
                                <?php if ($isAdminOrHR): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>

                        <tbody id="employeeTableBody">
                            <?php if (mysqli_num_rows($employeesResult) > 0): ?>
                                <?php while ($employee = mysqli_fetch_assoc($employeesResult)): ?>
                                    <!-- DINAGDAGAN NG CLASS AT DATA-HREF ANG TR TAG SA IBABA -->
                                    <tr class="clickable-row" data-href="employee_profile.php?id=<?php echo $employee['id']; ?>">
                                        
                                        <!-- NAME -->
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar blue-bg">
                                                    <?php echo strtoupper(substr($employee['firstname'], 0, 1) . substr($employee['lastname'], 0, 1)); ?>
                                                </div>
                                                <?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?>
                                            </div>
                                        </td>

                                        <!-- DEPARTMENT -->
                                        <td><?php echo htmlspecialchars($employee['department']); ?></td>

                                        <!-- POSITION -->
                                        <td><?php echo htmlspecialchars($employee['position']); ?></td>

                                        <!-- STATUS -->
                                        <td><?php echo htmlspecialchars($employee['employment_status']); ?></td>

                                        <!-- ACTIONS -->
                                        <?php if ($isAdminOrHR): ?>
                                            <td class="action-buttons">
                                                <button 
                                                    type="button" 
                                                    class="edit-btn" 
                                                    onclick="window.location.href='employee.php?edit_id=<?php echo $employee['id']; ?>'"
                                                >
                                                    Edit
                                                </button>

                                                <button
                                                    type="button"
                                                    class="employee-delete-btn delete-btn"
                                                    data-id="<?php echo $employee['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-type="employee"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo $isAdminOrHR ? '5' : '4'; ?>" style="text-align: center;">
                                        No employees found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end .employee-container -->
    </main>
</div>

<!-- ERROR MODAL -->
<div id="errorModal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-content">
        <h3 id="modalTitle">Employee Notice</h3>
        <p id="modalMessage">Please complete the form.</p>
        <button type="button" id="closeModalBtn">Close</button>
    </div>
</div>

<!-- DELETE MODAL -->
<?php if ($isAdminOrHR): ?>
    <div id="deleteModal" class="modal hidden">
        <div class="modal-content">
            <h3>Delete Employee</h3>
            <p id="deleteMessage">Are you sure you want to delete this employee?</p>
            <div class="modal-buttons">
                <button id="confirmDeleteBtn" class="delete-btn" type="button">Ok</button>
                <button id="cancelDeleteBtn" class="primary-btn" type="button">Cancel</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- EMPLOYEE SUCCESS MODAL -->
<div id="employeeModal" class="modal hidden">
    <div class="modal-content">
        <h3 id="employeeModalTitle"></h3>
        <p id="employeeModalMessage"></p>
        <button id="closeEmployeeModal" type="button">OK</button>
    </div>
</div>

<script>
    // 1. Error Modal Handler
    window.onload = function () {
        const errorMessage = <?php echo json_encode($_SESSION['employee_error'] ?? ''); ?>;

        // LALABAS LANG ANG MODAL KUNG MAY HINDI EMPTY NA ERROR
        if (errorMessage && errorMessage.trim() !== '') {
            const modalTitle   = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const errorModal   = document.getElementById('errorModal');

            if (modalTitle) modalTitle.textContent = 'Duplicate Employee ID';
            if (modalMessage) modalMessage.textContent = errorMessage;
            if (errorModal) errorModal.classList.remove('hidden');
        }
    };

    // 2. Clickable Row Handler
    document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================
    // 1. LIVE SEARCH FILTER WITH "NO RESULTS FOUND"
    // ==========================================
    const searchInput = document.getElementById('searchInput') || document.querySelector('input[name="search"]') || document.querySelector('.search-input');
    const tableBody   = document.getElementById('employeeTableBody');

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function () {
            const filter = this.value.toLowerCase().trim();
            const rows   = tableBody.querySelectorAll('tr.clickable-row');
            let visibleCount = 0;

            rows.forEach(row => {
                // Kunin LANG ang First Name at Last Name cell (unang column) 
                // at kung may Employee ID man sa row
                const nameCell = row.querySelector('.employee-name') || row.children[0];
                const searchableText = nameCell ? nameCell.textContent.toLowerCase() : row.textContent.toLowerCase();

                if (searchableText.includes(filter)) {
                    row.style.display = ''; // Ipakita ang row
                    visibleCount++;
                } else {
                    row.style.display = 'none'; // Itago ang row
                }
            });

            // "No Employee Found" Row Handler
            let noMatchRow = document.getElementById('noMatchRow');

            if (visibleCount === 0 && filter !== '') {
                if (!noMatchRow) {
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'noMatchRow';
                    const colCount = rows[0] ? rows[0].children.length : 5; 
                    noMatchRow.innerHTML = `<td colspan="${colCount}" style="text-align: center; padding: 15px; color: #888;">No employees found.</td>`;
                    tableBody.appendChild(noMatchRow);
                } else {
                    noMatchRow.style.display = '';
                }
            } else if (noMatchRow) {
                noMatchRow.style.display = 'none';
            }
        });
    }

    // ==========================================
    // 2. ERROR MODAL HANDLER
    // ==========================================
    const errorMessage = <?php echo json_encode($_SESSION['employee_error'] ?? ''); ?>;
    if (errorMessage && errorMessage.trim() !== '') {
        const modalTitle   = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const errorModal   = document.getElementById('errorModal');

        if (modalTitle) modalTitle.textContent = 'Duplicate Employee ID';
        if (modalMessage) modalMessage.textContent = errorMessage;
        if (errorModal) errorModal.classList.remove('hidden');
    }
});

// ==========================================
// 3. CLICKABLE TABLE ROWS
// ==========================================
document.addEventListener('click', function (e) {
    const row = e.target.closest('.clickable-row');
    
    if (row && !e.target.closest('button, a, input')) {
        const targetUrl = row.getAttribute('data-href');
        if (targetUrl) {
            window.location.href = targetUrl;
        }
    }
});


</script>

<script src="script.js"></script>

<!-- ERROR SESSION HANDLER -->
<?php if (isset($_SESSION['employee_error'])): ?>

<?php unset($_SESSION['employee_error']); ?>
<?php endif; ?>

</body>
</html>