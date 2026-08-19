<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/departments.css">
    <title>Departments | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php
            session_start();

            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit();
            }

            $currentPage = basename($_SERVER['PHP_SELF']);
            $userRole = $_SESSION['role'] ?? '';

            require_once 'database.php';

            $departmentModalTitle = '';
            $departmentModalMessage = '';

            if (isset($_GET['success'])) {
                if ($_GET['success'] === 'added') {
                    $departmentModalTitle = 'Success';
                    $departmentModalMessage = 'Department added successfully.';
                }
            }

            if (isset($_GET['error'])) {
                if ($_GET['error'] === 'duplicate') {
                    $departmentModalTitle = 'Duplicate Department';
                    $departmentModalMessage = 'This department already exists.';
                } elseif ($_GET['error'] === 'empty') {
                    $departmentModalTitle = 'Invalid Input';
                    $departmentModalMessage = 'Please enter a department name.';
                } elseif ($_GET['error'] === 'failed') {
                    $departmentModalTitle = 'Error';
                    $departmentModalMessage = 'Failed to add department. Please try again.';
                }
            }

            // ==========================
            // DEPARTMENT SUMMARY
            // ==========================

            // Total Departments
            $totalDepartments = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT COUNT(*) AS total FROM departments")
            )['total'] ?? 0;

            // Total Employees
            $totalEmployees = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees")
            )['total'] ?? 0;

            // Department Heads
            $departmentHeads = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT COUNT(*) AS total
                    FROM departments
                    WHERE department_head IS NOT NULL
                    AND department_head != ''"
                )
            )['total'] ?? 0;

            // ==========================
            // SEARCH QUERY (STARTS WITH)
            // ==========================
            $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
            $searchParam = $searchTerm . '%'; // Nagsisimula lamang sa tinype na letra

            if (!empty($searchTerm)) {
                $departmentStmt = mysqli_prepare(
                    $conn,
                    "SELECT id, department_name, department_head, status
                    FROM departments
                    WHERE department_name LIKE ? OR department_head LIKE ?
                    ORDER BY department_name ASC"
                );
                mysqli_stmt_bind_param($departmentStmt, "ss", $searchParam, $searchParam);
                mysqli_stmt_execute($departmentStmt);
                $departmentQuery = mysqli_stmt_get_result($departmentStmt);
            } else {
                $departmentQuery = mysqli_query(
                    $conn,
                    "SELECT id, department_name, department_head, status
                    FROM departments
                    ORDER BY department_name ASC"
                );
            }
        ?>

        <?php include 'sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="department-container">
                    
                    <!-- PAGE HEADER & SEARCH TOOL -->
                    <!-- PAGE HEADER & SEARCH TOOL -->
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Organization Management</p>
                            <h1>Departments</h1>
                        </div>

                        <!-- PINSAMA SA LOOB NG HEADER-ACTIONS -->
                        <div class="header-actions">
                            <div class="table-tools">
                                <div class="employee-search-form">
                                    <input class="search-input" id="liveSearchInput" type="text" placeholder="Search department..." autocomplete="off">
                                    <button class="search-btn" id="liveSearchBtn" type="button">Search</button>
                                </div>
                            </div>

                            <button type="button" class="primary-btn" id="addDepartmentBtn">
                                + Add Department
                            </button>
                        </div>
                    </div>

                    <!-- ADD DEPARTMENT FORM -->
                    <div id="addDepartmentFormPanel" class="employee-form-panel hidden">
                        <div class="panel-header">
                            <h2>Add New Department</h2>
                        </div>
                        <form action="add_department_process.php" method="POST" id="addDepartmentForm">
                            <div class="input-group">
                                <label for="departmentName">Department Name</label>
                                <input type="text" id="departmentName" name="department_name" class="inputs" 
                                    placeholder="Enter department name" required
                                >
                            </div>

                            <div class="input-group">
                                <label for="departmentHead">Department Head</label>
                                <input type="text" id="departmentHead" name="department_head" class="inputs" 
                                    placeholder="Enter department head"
                                >
                            </div>

                            <div class="input-group">
                                <label for="departmentStatus">Status</label>
                                <select id="departmentStatus" name="status" class="inputs" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="primary-btn">Save</button>
                                <button type="button" class="primary-btn" id="cancelAddDepartmentBtn">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- SUMMARY CARDS -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Total Departments</h3>
                            <p><?php echo $totalDepartments; ?></p>
                        </div>

                        <div class="summary-card green">
                            <h3>Total Employees</h3>
                            <p><?php echo $totalEmployees; ?></p>
                        </div>

                        <div class="summary-card purple">
                            <h3>Department Heads</h3>
                            <p><?php echo $departmentHeads; ?></p>
                        </div>
                    </div>

                    <!-- DEPARTMENT TABLE DIRECTORY -->
                    <div class="panel-header">
                        <h2>Department Directory</h2>
                    </div>
                    
                    <div class="employee-panel">
                        <table class="dashboard-table employee-table">
                            <thead id="thead">
                                <tr>
                                    <th>Department</th>
                                    <th>Department Head</th>
                                    <th>Total Employees</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="departmentTableBody">
                            <?php if ($departmentQuery && mysqli_num_rows($departmentQuery) > 0): ?>
                                <?php while ($department = mysqli_fetch_assoc($departmentQuery)): ?>
                                    <?php
                                        $departmentName = $department['department_name'];
                                        $departmentHead = $department['department_head'];
                                        $status = $department['status'];

                                        // Generate department initials
                                        $words = explode(' ', $departmentName);
                                        $initials = '';
                                        foreach ($words as $word) {
                                            if (!empty($word)) {
                                                $initials .= strtoupper(substr($word, 0, 1));
                                            }
                                        }
                                        $initials = substr($initials, 0, 2);

                                        // Status CSS class
                                        switch ($status) {
                                            case 'Active':
                                                $statusClass = 'present';
                                                break;
                                            case 'Inactive':
                                                $statusClass = 'absent';
                                                break;
                                            default:
                                                $statusClass = 'pending';
                                        }
                                    ?>
                                    <tr>
                                        <!-- Department -->
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar blue-bg">
                                                    <?php echo htmlspecialchars($initials); ?>
                                                </div>
                                                <?php echo htmlspecialchars($departmentName); ?>
                                            </div>
                                        </td>
                                        
                                        <!-- Department Head -->
                                        <td>
                                            <?php
                                                echo !empty($departmentHead)
                                                    ? htmlspecialchars($departmentHead)
                                                    : 'Not Assigned';
                                            ?>
                                        </td>
                                        
                                        <!-- Total Employees -->
                                        <td>
                                            <?php
                                                $employeeCountQuery = mysqli_query(
                                                    $conn,
                                                    "SELECT COUNT(*) AS total
                                                    FROM employees
                                                    WHERE department = '" . mysqli_real_escape_string($conn, $departmentName) . "'"
                                                );
                                                $employeeCount = mysqli_fetch_assoc($employeeCountQuery)['total'] ?? 0;
                                                echo $employeeCount;
                                            ?>
                                        </td>
                                        
                                        <!-- Status -->
                                        <td>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        
                                        <!-- Action -->
                                        <td class="action-buttons">
                                            <button type="button" class="edit-btn" onclick="window.location.href='edit_department.php?id=<?php echo $department['id']; ?>'">
                                                Edit
                                            </button>
                                            <button type="button" class="employee-delete-btn delete-btn" 
                                                data-id="<?php echo $department['id']; ?>"
                                                data-type="department"
                                                data-name="<?php echo htmlspecialchars($departmentName); ?>">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">
                                        No departments found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- DELETE DEPARTMENT MODAL -->
    <div id="deleteModal" class="modal hidden">
        <div class="modal-content">
            <div class="panel-header">
                <h2>Delete Department</h2>
            </div>
            <p id="deleteMessage">Are you sure you want to delete this department?</p>
            <div class="form-actions">
                <button type="button" class="primary-btn" id="confirmDeleteBtn">Delete</button>
                <button type="button" class="primary-btn" id="cancelDeleteBtn">Cancel</button>
            </div>
        </div>
    </div>

    <!-- DEPARTMENT RESULT MODAL -->
    <div id="departmentResultModal" class="modal hidden">
        <div class="modal-content">
            <div class="panel-header">
                <h2 id="departmentModalTitle">Department</h2>
            </div>
            <p id="departmentModalMessage">Department action completed.</p>
            <div class="form-actions">
                <button type="button" class="primary-btn" id="closeDepartmentModalBtn">OK</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('departmentResultModal');
        const modalTitle = document.getElementById('departmentModalTitle');
        const modalMessage = document.getElementById('departmentModalMessage');
        const closeBtn = document.getElementById('closeDepartmentModalBtn');
        const title = <?php echo json_encode($departmentModalTitle); ?>;
        const message = <?php echo json_encode($departmentModalMessage); ?>;

        if (title && message && modal) {
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            modal.classList.remove('hidden');
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
                window.history.replaceState(
                    {},
                    document.title,
                    window.location.pathname
                );
            });
        }
    });
    </script>
    <script src="script.js"></script>
</body>
</html>