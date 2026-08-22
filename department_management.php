<?php

session_start();
require_once 'database.php';

// 1. Require login — bounce guests to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Current logged-in user
$userId   = intval($_SESSION['user_id']);
$userRole = strtolower(trim($_SESSION['role'] ?? ''));

// Only Department Heads may view this page
$validHeadRoles = ['department head', 'dept head', 'dept_head'];
$isDepartmentHead = in_array($userRole, $validHeadRoles, true);

if (!$isDepartmentHead) {
    header("Location: dashboard.php");
    exit();
}

// Name and Email of the logged-in department head
$headName       = $_SESSION['fullname'] ?? '';
$headEmail      = $_SESSION['email'] ?? '';
$headDepartment = '';

/* ------------------------------------------------------------
   1. FIND WHICH DEPARTMENT THIS PERSON HEADS
   Order of Search:
   a. Check employees table via Email
   b. Check departments table via Head Name
   c. Session fallback
   ------------------------------------------------------------ */

// Step A: Kunin ang department mula sa employees table gamit ang Email
if (!empty($headEmail)) {
    $deptQuery = mysqli_prepare($conn, "SELECT department FROM employees WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1");
    if ($deptQuery) {
        mysqli_stmt_bind_param($deptQuery, "s", $headEmail);
        mysqli_stmt_execute($deptQuery);
        $deptRes = mysqli_stmt_get_result($deptQuery);
        if ($deptRow = mysqli_fetch_assoc($deptRes)) {
            $headDepartment = trim($deptRow['department'] ?? '');
        }
        mysqli_stmt_close($deptQuery);
    }
}

// Step B: Kung walang nahanap sa employees, i-check sa departments table
// FIXED: tinanggal ang "head_email" column na hindi umiiral sa departments table
if (empty($headDepartment) && !empty($headName)) {
    $departmentStmt = mysqli_prepare(
        $conn,
        "SELECT department_name
         FROM departments
         WHERE LOWER(TRIM(department_head)) = LOWER(TRIM(?))
         LIMIT 1"
    );

    if ($departmentStmt) {
        mysqli_stmt_bind_param($departmentStmt, "s", $headName);
        mysqli_stmt_execute($departmentStmt);
        $departmentResult = mysqli_stmt_get_result($departmentStmt);

        if ($departmentResult && mysqli_num_rows($departmentResult) > 0) {
            $departmentData = mysqli_fetch_assoc($departmentResult);
            $headDepartment = trim($departmentData['department_name'] ?? '');
        }

        mysqli_stmt_close($departmentStmt);
    }
}

// Step C: Fallback sa Session kung walang mahanap sa DB
if (empty($headDepartment)) {
    $headDepartment = trim($_SESSION['department'] ?? '');
}

// I-update ang session para aligned sa lahat ng pages
$_SESSION['department'] = $headDepartment;


/* ------------------------------------------------------------
   2. EMPLOYEES LIST — All employees belonging to head's department
   ------------------------------------------------------------ */
$employees = [];

if (!empty($headDepartment)) {
    $employeeStmt = mysqli_prepare(
        $conn,
        "SELECT id, employee_id, firstname, lastname, department, position, employment_status, email, phone
         FROM employees
         WHERE LOWER(TRIM(department)) = LOWER(TRIM(?))
         ORDER BY firstname ASC, lastname ASC"
    );

    if ($employeeStmt) {
        mysqli_stmt_bind_param($employeeStmt, "s", $headDepartment);
        mysqli_stmt_execute($employeeStmt);
        $employeeResult = mysqli_stmt_get_result($employeeStmt);

        if ($employeeResult) {
            while ($row = mysqli_fetch_assoc($employeeResult)) {
                $employees[] = $row;
            }
        }
        mysqli_stmt_close($employeeStmt);
    }
}


/* ------------------------------------------------------------
   3. ATTENDANCE — Filtered by department and selected date
   ------------------------------------------------------------ */
$inputDate = $_GET['attendance_date'] ?? '';
if (!empty($inputDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $inputDate)) {
    $selectedAttendanceDate = $inputDate;
} else {
    $selectedAttendanceDate = date('Y-m-d');
}

$attendanceRecords = [];
$today = date('Y-m-d');

if (!empty($headDepartment)) {
    $attendanceStmt = mysqli_prepare(
        $conn,
        "SELECT
            employees.id AS employee_id,
            employees.employee_id AS employee_code,
            employees.firstname,
            employees.lastname,
            employees.department,
            attendance.attendance_date,
            attendance.time_in,
            attendance.time_out,
            attendance.status
         FROM employees
         LEFT JOIN attendance
            ON attendance.employee_id = employees.id
            AND attendance.attendance_date = ?
         WHERE LOWER(TRIM(employees.department)) = LOWER(TRIM(?))
         ORDER BY employees.firstname ASC, employees.lastname ASC"
    );

    if ($attendanceStmt) {
        mysqli_stmt_bind_param($attendanceStmt, "ss", $selectedAttendanceDate, $headDepartment);
        mysqli_stmt_execute($attendanceStmt);
        $attendanceResult = mysqli_stmt_get_result($attendanceStmt);

        if ($attendanceResult) {
            while ($row = mysqli_fetch_assoc($attendanceResult)) {
                if (empty($row['attendance_date'])) {
                    $row['attendance_date'] = $selectedAttendanceDate;
                    if ($selectedAttendanceDate > $today) {
                        $row['status'] = 'No Record';
                    } else {
                        $row['status'] = 'Absent';
                    }
                } elseif (empty($row['status'])) {
                    $row['status'] = 'Pending';
                }

                $attendanceRecords[] = $row;
            }
        }

        mysqli_stmt_close($attendanceStmt);
    }
}


/* ------------------------------------------------------------
   4. LEAVE REQUESTS — Filtered by head's department
   ------------------------------------------------------------ */
$leaveRecords = [];

if (!empty($headDepartment)) {
    $leaveStmt = mysqli_prepare(
        $conn,
        "SELECT
            leave_requests.id,
            leave_requests.employee_id,
            leave_requests.leave_type,
            leave_requests.start_date,
            leave_requests.end_date,
            leave_requests.status,
            leave_requests.reason,
            employees.employee_id AS employee_code,
            employees.firstname,
            employees.lastname,
            employees.department
         FROM leave_requests
         INNER JOIN employees ON employees.id = leave_requests.employee_id
         WHERE LOWER(TRIM(employees.department)) = LOWER(TRIM(?))
         ORDER BY leave_requests.start_date DESC"
    );

    if ($leaveStmt) {
        mysqli_stmt_bind_param($leaveStmt, "s", $headDepartment);
        mysqli_stmt_execute($leaveStmt);
        $leaveResult = mysqli_stmt_get_result($leaveStmt);

        if ($leaveResult) {
            while ($row = mysqli_fetch_assoc($leaveResult)) {
                $leaveRecords[] = $row;
            }
        }

        mysqli_stmt_close($leaveStmt);
    }
}


/* ------------------------------------------------------------
   5. SCHEDULES — Filtered by head's department (and optional date)
   ------------------------------------------------------------ */
$scheduleRecords      = [];
$selectedScheduleDate = $_GET['schedule_date'] ?? '';

if (!empty($headDepartment)) {
    $scheduleBaseSql = "SELECT
            schedules.id,
            schedules.employee_id,
            schedules.schedule_date,
            schedules.time_in,
            schedules.time_out,
            schedules.break_start,
            schedules.break_end,
            schedules.created_at,
            employees.employee_id AS employee_code,
            employees.firstname,
            employees.lastname,
            employees.department,
            employees.position
         FROM schedules
         INNER JOIN employees ON employees.id = schedules.employee_id
         WHERE LOWER(TRIM(employees.department)) = LOWER(TRIM(?))";

    if (!empty($selectedScheduleDate)) {
        $scheduleStmt = mysqli_prepare(
            $conn,
            $scheduleBaseSql . " AND schedules.schedule_date = ? ORDER BY employees.firstname ASC, employees.lastname ASC"
        );

        if ($scheduleStmt) {
            mysqli_stmt_bind_param($scheduleStmt, "ss", $headDepartment, $selectedScheduleDate);
            mysqli_stmt_execute($scheduleStmt);
            $scheduleResult = mysqli_stmt_get_result($scheduleStmt);

            if ($scheduleResult) {
                while ($row = mysqli_fetch_assoc($scheduleResult)) {
                    $scheduleRecords[] = $row;
                }
            }
            mysqli_stmt_close($scheduleStmt);
        }
    } else {
        $scheduleStmt = mysqli_prepare(
            $conn,
            $scheduleBaseSql . " ORDER BY schedules.schedule_date DESC, employees.firstname ASC, employees.lastname ASC"
        );

        if ($scheduleStmt) {
            mysqli_stmt_bind_param($scheduleStmt, "s", $headDepartment);
            mysqli_stmt_execute($scheduleStmt);
            $scheduleResult = mysqli_stmt_get_result($scheduleStmt);

            if ($scheduleResult) {
                while ($row = mysqli_fetch_assoc($scheduleResult)) {
                    $scheduleRecords[] = $row;
                }
            }
            mysqli_stmt_close($scheduleStmt);
        }
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/department_management.css">
    <title>Department Management | HR Portal</title>
</head>
<body class="dashboard-page">

<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="dashboard-container">
            <div class="department-management-page">

                <!-- Page header -->
                <div class="department-management-header">
                    <h1>Department Management</h1>
                    <p><?php echo htmlspecialchars($headDepartment); ?> Department</p>
                </div>

                <!-- Tab-style cards -->
                <div class="management-panel">
                    <div class="management-panel-description">
                        Select a section below to manage your department.
                    </div>

                    <div class="management-cards">
                        <button type="button" class="management-card active" data-section="employees">
                            <img src="images/users.png" alt="">
                            <span>Employees</span>
                        </button>

                        <button type="button" class="management-card" data-section="attendance">
                            <img src="images/clock.png" alt="">
                            <span>Attendance</span>
                        </button>

                        <button type="button" class="management-card" data-section="leave">
                            <img src="images/calendar-month.png" alt="">
                            <span>Leave Requests</span>
                        </button>

                        <button type="button" class="management-card" data-section="schedule">
                            <img src="images/calendar-month.png" alt="">
                            <span>Schedule</span>
                        </button>
                    </div>
                </div>

                <div class="management-content">

                    <!-- ============ EMPLOYEES TABLE ============ -->
                    <section class="management-section active" id="section-employees">
                        <div class="management-section-header">
                            <div>
                                <h2>Employees</h2>
                                <p>Employees under <?php echo htmlspecialchars($headDepartment); ?></p>
                            </div>
                        </div>

                        <div class="management-table-wrapper">
                            <table class="management-table">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($employees) > 0): ?>
                                        <?php foreach ($employees as $employee): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['position'] ?? '--'); ?></td>
                                                <td><?php echo htmlspecialchars($employee['department'] ?? '--'); ?></td>
                                                <td>
                                                    <?php
                                                    $employeeStatus = $employee['employment_status'] ?? 'Unknown';
                                                    $statusClass    = strtolower(str_replace(' ', '-', $employeeStatus));
                                                    ?>
                                                    <span class="status-badge status-<?php echo htmlspecialchars($statusClass); ?>">
                                                        <?php echo htmlspecialchars($employeeStatus); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="empty-row">No employees found in this department.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ============ ATTENDANCE TABLE ============ -->
                    <section class="management-section" id="section-attendance">
                        <div class="management-section-header">
                            <div>
                                <h2>Attendance</h2>
                                <p>Attendance records of your department</p>
                            </div>

                            <form method="GET" class="schedule-date-filter">
                                <label for="attendance_date">Date</label>
                                <input type="date" id="attendance_date" name="attendance_date" value="<?php echo htmlspecialchars($selectedAttendanceDate); ?>" onchange="this.form.submit()">
                            </form>
                        </div> 

                        <div class="management-table-wrapper">
                            <table class="management-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($attendanceRecords) > 0): ?>
                                        <?php foreach ($attendanceRecords as $attendance): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($attendance['firstname'] . ' ' . $attendance['lastname']); ?></td>
                                                <td>
                                                    <?php
                                                    echo !empty($attendance['attendance_date'])
                                                        ? date('M d, Y', strtotime($attendance['attendance_date']))
                                                        : '--';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo !empty($attendance['time_in'])
                                                        ? date('h:i A', strtotime($attendance['time_in']))
                                                        : '--';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo !empty($attendance['time_out'])
                                                        ? date('h:i A', strtotime($attendance['time_out']))
                                                        : '--';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $attendanceStatus = $attendance['status'] ?? 'Pending';
                                                    $attendanceClass  = strtolower(str_replace(' ', '-', $attendanceStatus));
                                                    ?>
                                                    <span class="status-badge status-<?php echo htmlspecialchars($attendanceClass); ?>">
                                                        <?php echo htmlspecialchars($attendanceStatus); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="empty-row">No attendance records found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ============ LEAVE REQUESTS TABLE ============ -->
                    <section class="management-section" id="section-leave">
                        <div class="management-section-header">
                            <div>
                                <h2>Leave Requests</h2>
                                <p>Leave requests from your department</p>
                            </div>
                        </div>

                        <div class="management-table-wrapper">
                            <table class="management-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($leaveRecords) > 0): ?>
                                        <?php foreach ($leaveRecords as $leave): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($leave['firstname'] . ' ' . $leave['lastname']); ?></td>
                                                <td><?php echo htmlspecialchars($leave['leave_type'] ?? '--'); ?></td>
                                                <td>
                                                    <?php
                                                    echo !empty($leave['start_date'])
                                                        ? date('M d, Y', strtotime($leave['start_date']))
                                                        : '--';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo !empty($leave['end_date'])
                                                        ? date('M d, Y', strtotime($leave['end_date']))
                                                        : '--';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $leaveStatus = $leave['status'] ?? 'Pending';
                                                    $leaveClass  = strtolower(str_replace(' ', '-', $leaveStatus));
                                                    ?>
                                                    <span class="status-badge status-<?php echo htmlspecialchars($leaveClass); ?>">
                                                        <?php echo htmlspecialchars($leaveStatus); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="empty-row">No leave requests found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ============ SCHEDULE TABLE ============ -->
                    <section class="management-section" id="section-schedule">
                        <div class="management-section-header">
                            <div>
                                <h2>Schedule</h2>
                                <p>Employee schedules of your department</p>
                            </div>

                            <form method="GET" class="schedule-date-filter">
                                <label for="schedule_date">Date</label>
                                <input
                                    type="date"
                                    id="schedule_date"
                                    name="schedule_date"
                                    value="<?php echo htmlspecialchars($selectedScheduleDate); ?>"
                                    onchange="this.form.submit()"
                                >
                            </form>
                        </div>

                        <div class="management-table-wrapper">
                            <table class="management-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Schedule Date</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($scheduleRecords) > 0): ?>
                                        <?php foreach ($scheduleRecords as $schedule): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($schedule['firstname'] . ' ' . $schedule['lastname']); ?></td>
                                                <td><?php echo htmlspecialchars($schedule['position'] ?? '--'); ?></td>
                                                <td>
                                                    <?php
                                                    echo !empty($schedule['schedule_date'])
                                                        ? date('M d, Y', strtotime($schedule['schedule_date']))
                                                        : '--';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo !empty($schedule['time_in'])
                                                        ? date('h:i A', strtotime($schedule['time_in']))
                                                        : '--';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo !empty($schedule['time_out'])
                                                        ? date('h:i A', strtotime($schedule['time_out']))
                                                        : '--';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="empty-row">
                                                <?php if (!empty($selectedScheduleDate)): ?>
                                                    No schedules found for <?php echo date('F d, Y', strtotime($selectedScheduleDate)); ?>.
                                                <?php else: ?>
                                                    No schedules found.
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.management-card');
    const sections = document.querySelectorAll('.management-section');

    function showSection(sectionName) {
        cards.forEach(function (card) {
            if (card.getAttribute('data-section') === sectionName) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });

        sections.forEach(function (section) {
            if (section.id === 'section-' + sectionName) {
                section.classList.add('active');
            } else {
                section.classList.remove('active');
            }
        });
    }

    // Auto-open attendance or schedule tab if filter was used
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('attendance_date')) {
        showSection('attendance');
    } else if (urlParams.has('schedule_date')) {
        showSection('schedule');
    } else {
        showSection('employees');
    }

    // Add click listeners to section cards
    cards.forEach(function (card) {
        card.addEventListener('click', function () {
            const section = this.getAttribute('data-section');
            showSection(section);
        });
    });
});
</script>
</body>
</html>