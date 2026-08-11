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
// CURRENT USER & ACCESS CHECK
// ==========================================================

$userId = intval($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? '';

$isAdminOrHR = ($userRole === 'Admin' || $userRole === 'HR');
$isDepartmentHead = ($userRole === 'Department Head');

if (!$isAdminOrHR && !$isDepartmentHead) {
    header("Location: dashboard.php");
    exit();
}

// ==========================================================
// GET EMPLOYEES
// ==========================================================

$employeesResult = null;

if ($isAdminOrHR) {
    $employeesResult = mysqli_query(
        $conn,
        "SELECT id, employee_id, firstname, lastname, department, position
         FROM employees
         ORDER BY firstname ASC, lastname ASC"
    );
} else {
    $headName = $_SESSION['fullname'] ?? '';
    $headDepartment = '';

    // Fetch department assigned to this Department Head
    $deptStmt = mysqli_prepare(
        $conn,
        "SELECT department_name
         FROM departments
         WHERE department_head = ? AND status = 'active'
         LIMIT 1"
    );

    if ($deptStmt) {
        mysqli_stmt_bind_param($deptStmt, "s", $headName);
        mysqli_stmt_execute($deptStmt);
        $deptRes = mysqli_stmt_get_result($deptStmt);
        if ($deptData = mysqli_fetch_assoc($deptRes)) {
            $headDepartment = $deptData['department_name'] ?? '';
        }
        mysqli_stmt_close($deptStmt);
    }

    // Fetch employees in this department
    $empStmt = mysqli_prepare(
        $conn,
        "SELECT id, employee_id, firstname, lastname, department, position
         FROM employees
         WHERE LOWER(department) = LOWER(?)
         ORDER BY firstname ASC, lastname ASC"
    );

    if ($empStmt) {
        mysqli_stmt_bind_param($empStmt, "s", $headDepartment);
        mysqli_stmt_execute($empStmt);
        $employeesResult = mysqli_stmt_get_result($empStmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/schedule.css">
    <title>Schedules | HR Dashboard</title>
</head>

<body class="dashboard-page">

<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="dashboard-container">
            <div class="schedule-container">

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div>
                        <p class="page-kicker">Workforce Management</p>
                        <h1>Employee Schedules</h1>
                    </div>

                    <?php if ($isAdminOrHR): ?>
                        <button type="button" class="primary-btn" id="addScheduleBtn">
                            + Add Schedule
                        </button>
                    <?php endif; ?>
                </div>

                <!-- SCHEDULE FORM -->
                <div class="schedule-form-panel" id="scheduleFormPanel">
                    <div class="form-header">
                        <div>
                            <p class="page-kicker">Schedule Management</p>
                            <h2>Create Employee Schedule</h2>
                        </div>
                    </div>

                    <form id="scheduleForm" action="save_schedule.php" method="POST">

                        <!-- EMPLOYEE INFORMATION -->
                        <div class="form-section">
                            <h3>Employee Information</h3>
                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="employee_id">Employee</label>
                                    <select name="employee_id" id="employee_id" required>
                                        <option value="">Select Employee</option>
                                        <?php if ($employeesResult && mysqli_num_rows($employeesResult) > 0): ?>
                                            <?php while ($emp = mysqli_fetch_assoc($employeesResult)): ?>
                                                <option
                                                    value="<?php echo htmlspecialchars($emp['id']); ?>"
                                                    data-department="<?php echo htmlspecialchars($emp['department'] ?? ''); ?>"
                                                    data-position="<?php echo htmlspecialchars($emp['position'] ?? ''); ?>"
                                                >
                                                    <?php echo htmlspecialchars($emp['firstname'] . ' ' . $emp['lastname'] . ' - ' . $emp['employee_id']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" id="department" name="department" readonly placeholder="Select employee first">
                                </div>

                                <div class="form-group">
                                    <label for="position">Position</label>
                                    <input type="text" id="position" name="position" readonly placeholder="Select employee first">
                                </div>

                            </div>
                        </div>

                        <!-- SCHEDULE DETAILS -->
                        <div class="form-section">
                            <h3>Schedule Details</h3>
                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="schedule_type">Schedule Type</label>
                                    <select name="schedule_type" id="schedule_type" required>
                                        <option value="">Select Schedule Type</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Flexible">Flexible</option>
                                        <option value="Custom">Custom</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="effective_date">Effective Date</label>
                                    <input type="date" name="effective_date" id="effective_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="schedule_status">Status</label>
                                    <select name="status" id="schedule_status" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- WORK DAYS -->
                        <div class="form-section">
                            <h3>Work Days</h3>
                            <div class="days-grid">
                                <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day): ?>
                                    <label class="day-option">
                                        <input type="checkbox" name="work_days[]" value="<?php echo $day; ?>">
                                        <span><?php echo $day; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- WORKING HOURS -->
                        <div class="form-section">
                            <h3>Working Hours</h3>
                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="time_in">Time In</label>
                                    <input type="time" name="time_in" id="time_in" required>
                                </div>

                                <div class="form-group">
                                    <label for="time_out">Time Out</label>
                                    <input type="time" name="time_out" id="time_out" required>
                                </div>

                                <div class="form-group">
                                    <label for="break_start">Break Start</label>
                                    <input
                                        type="time"
                                        name="break_start"
                                        id="break_start"
                                        disabled
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="break_end">Break End</label>
                                    <input
                                        type="time"
                                        name="break_end"
                                        id="break_end"
                                        disabled
                                    >
                                </div>

                            </div>
                        </div>

                        <!-- ADDITIONAL INFO -->
                        <div class="form-section">
                            <h3>Additional Information</h3>
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" rows="4" placeholder="Add notes about this schedule..."></textarea>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="form-actions">
                            <button type="button" class="secondary-btn" id="cancelScheduleBtn">Cancel</button>
                            <button type="submit" class="primary-btn">Save Schedule</button>
                        </div>

                    </form>
                </div>

                <!-- SCHEDULE DIRECTORY TABLE -->
                <div class="employee-panel" id="schedulePanel">
                    <div class="panel-header">
                        <h2>Schedule Directory</h2>
                    </div>

                    <table class="dashboard-table employee-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Schedule</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Effective Date</th>
                                <th>Status</th>
                                <?php if ($isAdminOrHR): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>

                        <?php

                        $scheduleSql = "
                            SELECT
                                schedules.id,
                                schedules.employee_id,
                                schedules.schedule_date,
                                schedules.time_in,
                                schedules.time_out,
                                schedules.break_start,
                                schedules.break_end,
                                employees.firstname,
                                employees.lastname,
                                employees.department,
                                employees.position
                            FROM schedules
                            INNER JOIN employees
                                ON schedules.employee_id = employees.id
                        ";

                        if ($isDepartmentHead) {
                            $scheduleSql .= "
                                WHERE LOWER(employees.department) = LOWER(?)
                            ";
                        }

                        $scheduleSql .= "
                            ORDER BY schedules.schedule_date DESC
                        ";

                        if ($isDepartmentHead) {

                            $scheduleStmt = mysqli_prepare($conn, $scheduleSql);

                            if ($scheduleStmt) {

                                mysqli_stmt_bind_param(
                                    $scheduleStmt,
                                    "s",
                                    $headDepartment
                                );

                                mysqli_stmt_execute($scheduleStmt);

                                $scheduleResult = mysqli_stmt_get_result($scheduleStmt);

                            } else {

                                $scheduleResult = false;

                            }

                        } else {

                            $scheduleResult = mysqli_query($conn, $scheduleSql);

                        }

                        ?>

                        <?php if ($scheduleResult && mysqli_num_rows($scheduleResult) > 0): ?>

                            <?php while ($schedule = mysqli_fetch_assoc($scheduleResult)): ?>

                                <tr>

                                    <!-- EMPLOYEE -->
                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $schedule['firstname'] . ' ' . $schedule['lastname']
                                        );
                                        ?>
                                    </td>

                                    <!-- DEPARTMENT -->
                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $schedule['department'] ?? ''
                                        );
                                        ?>
                                    </td>

                                    <!-- SCHEDULE -->
                                    <td>
                                        Regular
                                    </td>

                                    <!-- TIME IN -->
                                    <td>
                                        <?php
                                        echo !empty($schedule['time_in'])
                                            ? date("h:i A", strtotime($schedule['time_in']))
                                            : '-';
                                        ?>
                                    </td>

                                    <!-- TIME OUT -->
                                    <td>
                                        <?php
                                        echo !empty($schedule['time_out'])
                                            ? date("h:i A", strtotime($schedule['time_out']))
                                            : '-';
                                        ?>
                                    </td>

                                    <!-- EFFECTIVE DATE -->
                                    <td>
                                        <?php
                                        echo !empty($schedule['schedule_date'])
                                            ? date(
                                                "F d, Y",
                                                strtotime($schedule['schedule_date'])
                                            )
                                            : '-';
                                        ?>
                                    </td>

                                    <!-- STATUS -->
                                    <td>
                                        <span class="status-badge active">
                                            Active
                                        </span>
                                    </td>

                                    <?php if ($isAdminOrHR): ?>

                                        <!-- ACTIONS -->
                                        <td>

                                            <button
                                                type="button"
                                                class="edit-btn"
                                                onclick="editSchedule(<?php echo $schedule['id']; ?>)"
                                            >
                                                ✏️ Edit
                                            </button>

                                            <button
                                                type="button"
                                                class="employee-delete-btn"
                                                data-id="<?php echo $schedule['id']; ?>"
                                                data-type="schedule"
                                                data-name="<?php
                                                    echo htmlspecialchars(
                                                        $schedule['firstname'] . ' ' . $schedule['lastname']
                                                    );
                                                ?>"
                                            >
                                                🗑️ Delete
                                            </button>

                                        </td>

                                    <?php endif; ?>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="<?php echo $isAdminOrHR ? '8' : '7'; ?>"
                                    style="text-align: center;"
                                >
                                    No schedules found.
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

<!-- SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const addScheduleBtn = document.getElementById('addScheduleBtn');
    const cancelScheduleBtn = document.getElementById('cancelScheduleBtn');
    const scheduleFormPanel = document.getElementById('scheduleFormPanel');

    const employeeSelect = document.getElementById('employee_id');
    const departmentInput = document.getElementById('department');
    const positionInput = document.getElementById('position');


    // ==========================================================
    // ADD SCHEDULE BUTTON
    // ==========================================================

    if (addScheduleBtn && scheduleFormPanel) {

        addScheduleBtn.addEventListener('click', function () {

            scheduleFormPanel.classList.remove('hidden');

            // Scroll smoothly to the form
            scheduleFormPanel.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        });

    }


    // ==========================================================
    // CANCEL BUTTON
    // ==========================================================

    if (cancelScheduleBtn && scheduleFormPanel) {

        cancelScheduleBtn.addEventListener('click', function () {

            scheduleFormPanel.classList.add('hidden');

            // Optional: balik sa Schedule Directory
            const schedulePanel = document.getElementById('schedulePanel');

            if (schedulePanel) {
                schedulePanel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

        });

    }


    // ==========================================================
    // AUTO-FILL EMPLOYEE INFORMATION
    // ==========================================================

    if (employeeSelect) {

        employeeSelect.addEventListener('change', function () {

            const selectedOption =
                this.options[this.selectedIndex];

            if (selectedOption) {

                const department =
                    selectedOption.getAttribute('data-department') || '';

                const position =
                    selectedOption.getAttribute('data-position') || '';

                if (departmentInput) {
                    departmentInput.value = department;
                }

                if (positionInput) {
                    positionInput.value = position;
                }

            }

        });

    }

});
</script>

</body>
</html>