<?php

    session_start();
    include "database.php";

    // ==========================================================
    // LOGIN CHECK
    // ==========================================================

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }


    // ==========================================================
    // CURRENT USER
    // ==========================================================

    $userId = intval($_SESSION['user_id']);
    $userRole = $_SESSION['role'] ?? '';


    // ==========================================================
    // ROLE CHECK
    // ==========================================================

    $isAdminOrHR = ($userRole === 'Admin' || $userRole === 'HR');
    $isDepartmentHead = ($userRole === 'Department Head');
    $isEmployee = ($userRole === 'Employee');


    // ==========================================================
    // GET DEPARTMENT HEAD'S DEPARTMENT
    // ==========================================================

    $headDepartment = '';

    if ($isDepartmentHead) {
        $headName = $_SESSION['fullname'] ?? '';
        $departmentQuery = mysqli_prepare($conn, "SELECT department_name
            FROM departments
            WHERE department_head = ?
            AND LOWER(status) = 'active'
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
    // TODAY'S DATE
    // ==========================================================

    $today = date('Y-m-d');


    // ==========================================================
    // DEFAULT SUMMARY VALUES
    // ==========================================================

    $totalEmployees = 0;
    $presentToday = 0;
    $lateToday = 0;
    $absentToday = 0;


    // ==========================================================
    // ATTENDANCE SUMMARY
    // ==========================================================

    if ($isAdminOrHR) {

        // ======================================================
        // ADMIN / HR
        // ALL EMPLOYEES
        // ======================================================

        $totalEmployees = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees"))['total'];

        $presentToday = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) AS total
                FROM attendance
                WHERE attendance_date = '$today'
                AND status = 'Present'"
            )
        )['total'];


        $lateToday = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total
                FROM attendance
                WHERE attendance_date = '$today'
                AND status = 'Late'"
            )
        )['total'];


        $absentToday = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total
                FROM employees e
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM attendance a
                    WHERE a.employee_id = e.id
                    AND a.attendance_date = '$today'
                )"
            )
        )['total'];


    } elseif ($isDepartmentHead) {

        // ======================================================
        // DEPARTMENT HEAD
        // OWN DEPARTMENT ONLY
        // ======================================================

        // TOTAL EMPLOYEES

        $countQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total
            FROM employees
            WHERE LOWER(department) = LOWER(?)"
        );

        mysqli_stmt_bind_param($countQuery, "s", $headDepartment);
        mysqli_stmt_execute($countQuery);
        $countResult = mysqli_stmt_get_result($countQuery);
        $countData = mysqli_fetch_assoc($countResult);
        $totalEmployees = $countData['total'] ?? 0;

        // ======================================================
        // PRESENT TODAY
        // ======================================================

        $presentQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total
            FROM attendance a
            INNER JOIN employees e
                ON a.employee_id = e.id
            WHERE LOWER(e.department) = LOWER(?)
            AND a.attendance_date = ?
            AND a.status = 'Present'"
        );

        mysqli_stmt_bind_param($presentQuery, "ss", $headDepartment, $today);
        mysqli_stmt_execute($presentQuery);

        $presentResult = mysqli_stmt_get_result($presentQuery);
        $presentData = mysqli_fetch_assoc($presentResult);
        $presentToday = $presentData['total'] ?? 0;


        // ======================================================
        // LATE TODAY
        // ======================================================

        $lateQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total
            FROM attendance a
            INNER JOIN employees e
                ON a.employee_id = e.id
            WHERE LOWER(e.department) = LOWER(?)
            AND a.attendance_date = ?
            AND a.status = 'Late'"
        );

        mysqli_stmt_bind_param($lateQuery, "ss", $headDepartment, $today);
        mysqli_stmt_execute($lateQuery);

        $lateResult = mysqli_stmt_get_result($lateQuery);
        $lateData = mysqli_fetch_assoc($lateResult);
        $lateToday = $lateData['total'] ?? 0;


        // ======================================================
        // ABSENT TODAY
        // ======================================================

        $absentQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total
            FROM employees e
            WHERE LOWER(e.department) = LOWER(?)
            AND NOT EXISTS (
                SELECT 1
                FROM attendance a
                WHERE a.employee_id = e.id
                AND a.attendance_date = ?
            )"
        );

        mysqli_stmt_bind_param($absentQuery, "ss", $headDepartment, $today);
        mysqli_stmt_execute($absentQuery);

        $absentResult = mysqli_stmt_get_result($absentQuery);
        $absentData = mysqli_fetch_assoc($absentResult);
        $absentToday = $absentData['total'] ?? 0;

    }


    // ==========================================================
    // FORM MODE
    // ==========================================================

    $showForm = false;

    // + Record Attendance
    if (isset($_GET['show_form']) && $_GET['show_form'] === '1' && $isAdminOrHR) {
        $showForm = true;
    }

    // Edit Attendance
    if (isset($_GET['edit_id']) && $isAdminOrHR) {
        $showForm = true;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/attendance.css">
    <title>Attendance | HR Dashboard</title>
</head>

<body class="dashboard-page" data-show-form="<?php echo isset($_SESSION['attendance_error'])? '1' : '0';?>">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="attendance-container">
                    <div class="attendance-content">

                        <?php

                            // ==================================================
                            // SUCCESS MESSAGE
                            // ==================================================

                            if (isset($_SESSION['attendance_success'])) {
                                echo '<div class="success-message">'. htmlspecialchars($_SESSION['attendance_success']). '</div>';
                                unset($_SESSION['attendance_success']);
                            }

                            // ==================================================
                            // ERROR MESSAGE
                            // ==================================================

                            if (isset($_SESSION['attendance_error'])) {
                                echo '<div class="error-message">'. htmlspecialchars($_SESSION['attendance_error']). '</div>';
                                unset($_SESSION['attendance_error']);
                            }

                        ?>


                        <!-- PAGE HEADER -->

                        <div class="page-header">
                            <div>
                                <p class="page-kicker">Time & Attendance</p>
                                <h1>Attendance</h1>
                            </div>

                            <?php if ($isAdminOrHR): ?>
                                <button type="button" class="primary-btn" onclick="window.location.href='attendance.php?show_form=1'">
                                    + Record Attendance
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- FORM -->

                        <?php if ($showForm): ?>
                            <div class="attendance-form-wrapper">
                                <?php

                                    $isEditMode = isset($_GET['edit_id']);
                                    if ($isEditMode) {

                                        // ==================================================
                                        // GET ATTENDANCE RECORD
                                        // ==================================================

                                        $editId = intval($_GET['edit_id']);
                                        $attendanceSql = "SELECT * FROM attendance WHERE id = $editId";
                                        $attendanceResult = mysqli_query($conn, $attendanceSql);

                                        if (!$attendanceResult || mysqli_num_rows($attendanceResult) === 0) {
                                            die("Attendance record not found.");
                                        }

                                        $attendance = mysqli_fetch_assoc($attendanceResult);

                                        // ==================================================
                                        // GET EMPLOYEES
                                        // ==================================================

                                        $employeesSql = "SELECT id,firstname, lastname
                                            FROM employees
                                            ORDER BY
                                                firstname ASC,
                                                lastname ASC
                                        ";


                                        $employees = mysqli_query($conn, $employeesSql);

                                        if (!$employees) {
                                            die("Employee query failed: ". mysqli_error($conn));
                                        }

                                        include "edit_attendance.php";

                                    } else {

                                        // ==================================================
                                        // ADD ATTENDANCE
                                        // ==================================================

                                        include "add_attendance.php";

                                    }

                                ?>

                            </div>

                            <?php else: ?>

                            <!-- SUMMARY CARDS -->

                            <div class="employee-summary">
                                <div class="summary-card blue">
                                    <h3>Total Employees</h3>
                                    <p><?php echo $totalEmployees; ?></p>
                                </div>

                                <div class="summary-card green">
                                    <h3>Present Today</h3>
                                    <p><?php echo $presentToday; ?></p>
                                </div>

                                <div class="summary-card orange">
                                    <h3>Late Today</h3>
                                    <p><?php echo $lateToday; ?></p>
                                </div>

                                <div class="summary-card red">
                                    <h3>Absent Today</h3>
                                    <p><?php echo $absentToday;?></p>
                                </div>

                            </div>

                            <!-- ATTENDANCE TABLE -->
                            <div class="panel-header">
                                <h2>Today's Attendance</h2>
                            </div>
                            <div class="employee-panel">
                                <table class="dashboard-table employee-table">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Department</th>
                                            <th>Date</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Status</th>

                                            <?php if ($isAdminOrHR): ?>
                                                <th>Actions</th>
                                            <?php endif; ?>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php

                                            // ==================================================
                                            // ATTENDANCE QUERY
                                            // ==================================================

                                            if ($isAdminOrHR) {

                                                // ==================================================
                                                // ADMIN / HR
                                                // SEE ALL ATTENDANCE
                                                // ==================================================

                                                $sql = "SELECT attendance.*,
                                                        employees.firstname,
                                                        employees.lastname,
                                                        employees.department

                                                    FROM attendance
                                                    INNER JOIN employees
                                                        ON attendance.employee_id = employees.id
                                                    ORDER BY
                                                        attendance.attendance_date DESC
                                                ";

                                                $result = mysqli_query($conn, $sql);

                                            } elseif ($isDepartmentHead) {

                                                // ==================================================
                                                // DEPARTMENT HEAD
                                                // SEE OWN DEPARTMENT ONLY
                                                // ==================================================

                                                $attendanceQuery = mysqli_prepare($conn, "SELECT attendance.*,
                                                            employees.firstname,
                                                            employees.lastname,
                                                            employees.department

                                                        FROM attendance
                                                        INNER JOIN employees ON attendance.employee_id = employees.id
                                                        WHERE LOWER(employees.department) = LOWER(?)
                                                        ORDER BY
                                                            attendance.attendance_date DESC"
                                                    );

                                                mysqli_stmt_bind_param($attendanceQuery, "s", $headDepartment);
                                                mysqli_stmt_execute($attendanceQuery);
                                                $result = mysqli_stmt_get_result($attendanceQuery);

                                            } else {

                                                // ==================================================
                                                // EMPLOYEE
                                                // SEE OWN ATTENDANCE ONLY
                                                // ==================================================

                                                $sql = "SELECT attendance.*,
                                                        employees.firstname,
                                                        employees.lastname,
                                                        employees.department

                                                    FROM attendance INNER JOIN employees ON attendance.employee_id = employees.id
                                                    INNER JOIN users ON users.employee_id = employees.id
                                                    WHERE users.id = $userId
                                                    ORDER BY attendance.attendance_date DESC
                                                ";


                                                $result = mysqli_query($conn, $sql);

                                            }


                                        // ==================================================
                                        // DISPLAY RECORDS
                                        // ==================================================

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {

                                                // ==================================================
                                                // INITIALS
                                                // ==================================================

                                                $initials = strtoupper(substr($row['firstname'], 0, 1).substr($row['lastname'], 0, 1));

                                                // ==================================================
                                                // STATUS BADGE
                                                // ==================================================

                                                switch ($row['status']) {

                                                    case "Present":
                                                        $badge = "present";
                                                        break;

                                                    case "Late":
                                                        $badge = "late";
                                                        break;

                                                    case "Absent":
                                                        $badge = "absent";
                                                        break;

                                                    case "On Leave":
                                                        $badge = "on-leave";
                                                        break;

                                                    default:
                                                        $badge = "pending";

                                                }

                                        ?>
                          
                                        <tr>
                                            <!-- EMPLOYEE -->
                                            <td>
                                                <div class="employee-name">
                                                    <div class="emp-avatar blue-bg">
                                                        <?php echo htmlspecialchars($initials);?>
                                                    </div>
                                                    <?php echo htmlspecialchars($row['firstname']. " ". $row['lastname']);?>
                                                </div>
                                            </td>

                                            <!-- DEPARTMENT -->

                                            <td>
                                                <?php echo htmlspecialchars($row['department']);?>
                                            </td>

                                            <!-- DATE -->

                                            <td>
                                                <?php echo date("F d, Y", strtotime($row['attendance_date']));?>
                                            </td>


                                            <!-- TIME IN -->

                                            <td>
                                                <?php echo htmlspecialchars($row['time_in'] ?? '');?>
                                            </td>

                                            <!-- TIME OUT -->

                                            <td>
                                                <?php echo htmlspecialchars($row['time_out'] ?? '');?>
                                            </td>

                                            <!-- STATUS -->

                                            <td>
                                                <span class="status-badge <?php echo $badge;?>">
                                                    <?php echo htmlspecialchars($row['status']);?>
                                                </span>
                                            </td>

                                            <!-- ACTIONS -->

                                            <?php if ($isAdminOrHR): ?>
                                                <td class="actions-cell">
                                                    <!-- EDIT -->

                                                    <form action="attendance.php" method="GET" style="display:inline;">
                                                        <input type="hidden" name="edit_id" value="<?php echo $row['id'];?>">
                                                        <button type="submit" class="edit-btn">
                                                            Edit
                                                        </button>
                                                    </form>

                                                    <!-- DELETE -->

                                                    <button type="button" class="employee-delete-btn" data-id="<?php echo $row['id']; ?>
                                                        " data-type="attendance" data-name="<?php echo htmlspecialchars($row['firstname']. ' '
                                                        . $row['lastname'], ENT_QUOTES, 'UTF-8');?>">
                                                        Delete
                                                    </button>

                                                </td>
                                            <?php endif; ?>
                                        </tr>

                                    <?php }} else { ?>
                                        <tr>
                                            <td colspan="<?php echo $isAdminOrHR ? '7' : '6'; ?>"style="text-align:center;">No attendance records found.</td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- ==========================================================
        DELETE MODAL
        ========================================================== -->

    <?php if ($isAdminOrHR): ?>
        <div id="deleteModal" class="modal hidden">
            <div class="modal-content">
                <h2>Delete Attendance</h2>
                <p id="deleteMessage">Are you sure you want to delete this attendance?</p>
                <div class="modal-actions">
                    <button type="button" id="cancelDeleteBtn">Cancel</button>
                    <button type="button" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script src="script.js"></script>
</body>
</html>