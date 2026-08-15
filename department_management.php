<?php

session_start();
require_once 'database.php';


/* ==========================================================
   LOGIN CHECK
   ========================================================== */

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


/* ==========================================================
   CURRENT USER
   ========================================================== */

$userId   = intval($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? '';


/* ==========================================================
   ROLE CHECK
   ========================================================== */

$isDepartmentHead = ($userRole === 'Department Head');

if (!$isDepartmentHead) {
    header("Location: dashboard.php");
    exit();
}


/* ==========================================================
   GET DEPARTMENT HEAD NAME
   ========================================================== */

$headName = $_SESSION['fullname'] ?? '';

$headDepartment = '';

$departmentStmt = mysqli_prepare(
    $conn,
    "SELECT department_name
     FROM departments
     WHERE department_head = ?
     AND status = 'active'
     LIMIT 1"
);

if ($departmentStmt) {

    mysqli_stmt_bind_param(
        $departmentStmt,
        "s",
        $headName
    );

    mysqli_stmt_execute($departmentStmt);

    $departmentResult = mysqli_stmt_get_result($departmentStmt);

    if ($departmentResult && mysqli_num_rows($departmentResult) > 0) {

        $departmentData = mysqli_fetch_assoc($departmentResult);

        $headDepartment = $departmentData['department_name'] ?? '';
    }

    mysqli_stmt_close($departmentStmt);
}


/* ==========================================================
   GET EMPLOYEES
   ========================================================== */

$employees = [];

$employeeStmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        employee_id,
        firstname,
        lastname,
        department,
        position,
        employment_status
     FROM employees
     WHERE LOWER(department) = LOWER(?)
     ORDER BY firstname ASC, lastname ASC"
);

if ($employeeStmt) {

    mysqli_stmt_bind_param(
        $employeeStmt,
        "s",
        $headDepartment
    );

    mysqli_stmt_execute($employeeStmt);

    $employeeResult = mysqli_stmt_get_result($employeeStmt);

    if ($employeeResult) {

        while ($row = mysqli_fetch_assoc($employeeResult)) {
            $employees[] = $row;
        }
    }

    mysqli_stmt_close($employeeStmt);
}


/* ==========================================================
   GET ATTENDANCE
   ========================================================== */

$attendanceRecords = [];

$attendanceStmt = mysqli_prepare(
    $conn,
    "SELECT
        attendance.id,
        attendance.employee_id,
        attendance.attendance_date,
        attendance.time_in,
        attendance.time_out,
        attendance.status,
        employees.employee_id AS employee_code,
        employees.firstname,
        employees.lastname,
        employees.department
     FROM attendance
     INNER JOIN employees
        ON employees.id = attendance.employee_id
     WHERE LOWER(employees.department) = LOWER(?)
     ORDER BY attendance.attendance_date DESC"
);

if ($attendanceStmt) {

    mysqli_stmt_bind_param(
        $attendanceStmt,
        "s",
        $headDepartment
    );

    mysqli_stmt_execute($attendanceStmt);

    $attendanceResult = mysqli_stmt_get_result($attendanceStmt);

    if ($attendanceResult) {

        while ($row = mysqli_fetch_assoc($attendanceResult)) {
            $attendanceRecords[] = $row;
        }
    }

    mysqli_stmt_close($attendanceStmt);
}


/* ==========================================================
   GET LEAVE REQUESTS
   ========================================================== */

$leaveRecords = [];

$leaveStmt = mysqli_prepare(
    $conn,
    "SELECT
        leave_requests.id,
        leave_requests.employee_id,
        leave_requests.leave_type,
        leave_requests.start_date,
        leave_requests.end_date,
        leave_requests.status,
        employees.employee_id AS employee_code,
        employees.firstname,
        employees.lastname,
        employees.department
     FROM leave_requests
     INNER JOIN employees
        ON employees.id = leave_requests.employee_id
     WHERE LOWER(employees.department) = LOWER(?)
     ORDER BY leave_requests.start_date DESC"
);

if ($leaveStmt) {

    mysqli_stmt_bind_param(
        $leaveStmt,
        "s",
        $headDepartment
    );

    mysqli_stmt_execute($leaveStmt);

    $leaveResult = mysqli_stmt_get_result($leaveStmt);

    if ($leaveResult) {

        while ($row = mysqli_fetch_assoc($leaveResult)) {
            $leaveRecords[] = $row;
        }
    }

    mysqli_stmt_close($leaveStmt);
}


/* ==========================================================
   GET SCHEDULES
   ========================================================== */

$scheduleRecords = [];

/*
|--------------------------------------------------------------------------
| SELECTED SCHEDULE DATE
|--------------------------------------------------------------------------
| Kapag walang date na pinili, lahat ng schedules ang ipapakita.
| Kapag may date, iyon lang ang ipapakita.
*/

$selectedScheduleDate = $_GET['schedule_date'] ?? '';

if (!empty($selectedScheduleDate)) {

    $scheduleStmt = mysqli_prepare(
        $conn,
        "SELECT
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

         INNER JOIN employees
            ON employees.id = schedules.employee_id

         WHERE LOWER(employees.department) = LOWER(?)
         AND schedules.schedule_date = ?

         ORDER BY employees.firstname ASC,
                  employees.lastname ASC"
    );

    if ($scheduleStmt) {

        mysqli_stmt_bind_param(
            $scheduleStmt,
            "ss",
            $headDepartment,
            $selectedScheduleDate
        );

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
        "SELECT
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

         INNER JOIN employees
            ON employees.id = schedules.employee_id

         WHERE LOWER(employees.department) = LOWER(?)

         ORDER BY schedules.schedule_date DESC,
                  employees.firstname ASC,
                  employees.lastname ASC"
    );

    if ($scheduleStmt) {

        mysqli_stmt_bind_param(
            $scheduleStmt,
            "s",
            $headDepartment
        );

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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="styles/common.css"
    >
    <link
        rel="stylesheet"
        href="styles/department_management.css"
    >
    <title>Department Management | HR Portal</title>


    

</head>


<body class="dashboard-page">


<div class="dashboard-shell">


    <?php include 'sidebar.php'; ?>


    <main class="dashboard-main">


        <div class="dashboard-container">


            <div class="department-management-page">


                <!-- ==================================================
                     PAGE HEADER
                     ================================================== -->

                <div class="department-management-header">

                    <h1>
                        Department Management
                    </h1>

                    <p>
                        <?php echo htmlspecialchars($headDepartment); ?>
                        Department
                    </p>

                </div>


                <!-- ==================================================
                     MANAGEMENT CARDS
                     ================================================== -->

                <div class="management-panel">

                    <div class="management-panel-description">

                        Select a section below to manage your department.

                    </div>


                    <div class="management-cards">


                        <!-- EMPLOYEES -->

                        <button
                            type="button"
                            class="management-card"
                            data-section="employees"
                        >

                            <img
                                src="images/users.png"
                                alt=""
                            >

                            <span>
                                Employees
                            </span>

                        </button>


                        <!-- ATTENDANCE -->

                        <button
                            type="button"
                            class="management-card"
                            data-section="attendance"
                        >

                            <img
                                src="images/clock.png"
                                alt=""
                            >

                            <span>
                                Attendance
                            </span>

                        </button>


                        <!-- LEAVE REQUESTS -->

                        <button
                            type="button"
                            class="management-card"
                            data-section="leave"
                        >

                            <img
                                src="images/calendar-month.png"
                                alt=""
                            >

                            <span>
                                Leave Requests
                            </span>

                        </button>


                        <!-- SCHEDULE -->

                        <button
                            type="button"
                            class="management-card"
                            data-section="schedule"
                        >

                            <img
                                src="images/calendar-month.png"
                                alt=""
                            >

                            <span>
                                Schedule
                            </span>

                        </button>


                    </div>

                </div>


                <!-- ==================================================
                     CONTENT AREA
                     ================================================== -->

                <div class="management-content">


                    <!-- =================================================
                         EMPLOYEE TABLE
                         ================================================= -->

                    <section
                        class="management-section"
                        id="section-employees"
                    >

                        <div class="management-section-header">

                            <div>

                                <h2>
                                    Employees
                                </h2>

                                <p>
                                    Employees under
                                    <?php echo htmlspecialchars($headDepartment); ?>
                                </p>

                            </div>

                        </div>


                        <div class="management-table-wrapper">

                            <table class="management-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Employee ID
                                        </th>

                                        <th>
                                            Employee
                                        </th>

                                        <th>
                                            Position
                                        </th>

                                        <th>
                                            Department
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if (count($employees) > 0): ?>

                                        <?php foreach ($employees as $employee): ?>

                                            <tr>

                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $employee['employee_id']
                                                    );
                                                    ?>
                                                </td>


                                                <td>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $employee['firstname']
                                                        . ' '
                                                        . $employee['lastname']
                                                    );
                                                    ?>

                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $employee['position'] ?? '--'
                                                    );
                                                    ?>
                                                </td>


                                                <td>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $employee['department'] ?? '--'
                                                    );
                                                    ?>
                                                </td>


                                                <td>

                                                    <?php

                                                    $employeeStatus =
                                                        $employee['employment_status']
                                                        ?? 'Unknown';

                                                    $statusClass =
                                                        strtolower(
                                                            str_replace(
                                                                ' ',
                                                                '-',
                                                                $employeeStatus
                                                            )
                                                        );

                                                    ?>

                                                    <span
                                                        class="status-badge status-<?php echo htmlspecialchars($statusClass); ?>"
                                                    >

                                                        <?php
                                                        echo htmlspecialchars(
                                                            $employeeStatus
                                                        );
                                                        ?>

                                                    </span>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="5"
                                                class="empty-row"
                                            >

                                                <?php if (!empty($selectedScheduleDate)): ?>

                                                    No schedules found for
                                                    <?php echo date('F d, Y', strtotime($selectedScheduleDate)); ?>.

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


                    <!-- =================================================
                         ATTENDANCE TABLE
                         ================================================= -->

                    <section
                        class="management-section"
                        id="section-attendance"
                    >

                        <div class="management-section-header">

                            <div>

                                <h2>
                                    Attendance
                                </h2>

                                <p>
                                    Attendance records of your department
                                </p>

                            </div>

                        </div>


                        <div class="management-table-wrapper">

                            <table class="management-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Employee
                                        </th>

                                        <th>
                                            Date
                                        </th>

                                        <th>
                                            Time In
                                        </th>

                                        <th>
                                            Time Out
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if (count($attendanceRecords) > 0): ?>

                                        <?php foreach ($attendanceRecords as $attendance): ?>

                                            <tr>

                                                <td>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $attendance['firstname']
                                                        . ' '
                                                        . $attendance['lastname']
                                                    );
                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $attendance['attendance_date']
                                                    )
                                                        ? date(
                                                            'M d, Y',
                                                            strtotime(
                                                                $attendance['attendance_date']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $attendance['time_in']
                                                    )
                                                        ? date(
                                                            'h:i A',
                                                            strtotime(
                                                                $attendance['time_in']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $attendance['time_out']
                                                    )
                                                        ? date(
                                                            'h:i A',
                                                            strtotime(
                                                                $attendance['time_out']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    $attendanceStatus =
                                                        $attendance['status']
                                                        ?? 'Pending';

                                                    $attendanceClass =
                                                        strtolower(
                                                            $attendanceStatus
                                                        );

                                                    ?>

                                                    <span
                                                        class="status-badge status-<?php echo htmlspecialchars($attendanceClass); ?>"
                                                    >

                                                        <?php
                                                        echo htmlspecialchars(
                                                            $attendanceStatus
                                                        );
                                                        ?>

                                                    </span>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="5"
                                                class="empty-row"
                                            >
                                                No attendance records found.
                                            </td>

                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </section>


                    <!-- =================================================
                         LEAVE REQUEST TABLE
                         ================================================= -->

                    <section
                        class="management-section"
                        id="section-leave"
                    >

                        <div class="management-section-header">

                            <div>

                                <h2>
                                    Leave Requests
                                </h2>

                                <p>
                                    Leave requests from your department
                                </p>

                            </div>

                        </div>


                        <div class="management-table-wrapper">

                            <table class="management-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Employee
                                        </th>

                                        <th>
                                            Leave Type
                                        </th>

                                        <th>
                                            Start Date
                                        </th>

                                        <th>
                                            End Date
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if (count($leaveRecords) > 0): ?>

                                        <?php foreach ($leaveRecords as $leave): ?>

                                            <tr>

                                                <td>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $leave['firstname']
                                                        . ' '
                                                        . $leave['lastname']
                                                    );
                                                    ?>

                                                </td>


                                                <td>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $leave['leave_type']
                                                        ?? '--'
                                                    );
                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $leave['start_date']
                                                    )
                                                        ? date(
                                                            'M d, Y',
                                                            strtotime(
                                                                $leave['start_date']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $leave['end_date']
                                                    )
                                                        ? date(
                                                            'M d, Y',
                                                            strtotime(
                                                                $leave['end_date']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    $leaveStatus =
                                                        $leave['status']
                                                        ?? 'Pending';

                                                    $leaveClass =
                                                        strtolower(
                                                            $leaveStatus
                                                        );

                                                    ?>

                                                    <span
                                                        class="status-badge status-<?php echo htmlspecialchars($leaveClass); ?>"
                                                    >

                                                        <?php
                                                        echo htmlspecialchars(
                                                            $leaveStatus
                                                        );
                                                        ?>

                                                    </span>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="5"
                                                class="empty-row"
                                            >
                                                No leave requests found.
                                            </td>

                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </section>


                    <!-- =================================================
                         SCHEDULE TABLE
                         ================================================= -->

                    <section
                        class="management-section"
                        id="section-schedule"
                    >

                        <div class="management-section-header">

                            <div>

                                <h2>
                                    Schedule
                                </h2>

                                <p>
                                    Employee schedules of your department
                                </p>

                            </div>


                            <!-- SCHEDULE DATE FILTER -->

                            <form
                                method="GET"
                                class="schedule-date-filter"
                            >

                                <label for="schedule_date">
                                    Date
                                </label>

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

                                        <th>
                                            Employee
                                        </th>

                                        <th>
                                            Position
                                        </th>

                                        <th>
                                            Schedule Date
                                        </th>

                                        <th>
                                            Time In
                                        </th>

                                        <th>
                                            Time Out
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if (count($scheduleRecords) > 0): ?>

                                        <?php foreach ($scheduleRecords as $schedule): ?>

                                            <tr>

                                                <td>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $schedule['firstname']
                                                        . ' '
                                                        . $schedule['lastname']
                                                    );
                                                    ?>

                                                </td>


                                                <td>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $schedule['position']
                                                        ?? '--'
                                                    );
                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $schedule['schedule_date']
                                                    )
                                                        ? date(
                                                            'M d, Y',
                                                            strtotime(
                                                                $schedule['schedule_date']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $schedule['time_in']
                                                    )
                                                        ? date(
                                                            'h:i A',
                                                            strtotime(
                                                                $schedule['time_in']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    echo !empty(
                                                        $schedule['time_out']
                                                    )
                                                        ? date(
                                                            'h:i A',
                                                            strtotime(
                                                                $schedule['time_out']
                                                            )
                                                        )
                                                        : '--';

                                                    ?>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="5"
                                                class="empty-row"
                                            >
                                                No schedules found.
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


<!-- ==========================================================
     JAVASCRIPT
     ========================================================== -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    const cards = document.querySelectorAll('.management-card');
    const sections = document.querySelectorAll('.management-section');


    /*
    ==========================================================
    FUNCTION: SHOW MANAGEMENT SECTION
    ==========================================================
    */

    function showSection(sectionName) {

        // Remove active from all cards
        cards.forEach(function (card) {
            card.classList.remove('active');
        });


        // Hide all sections
        sections.forEach(function (section) {
            section.classList.remove('active');
        });


        // Find selected card
        const selectedCard = document.querySelector(
            '.management-card[data-section="' + sectionName + '"]'
        );


        // Find selected section
        const selectedSection = document.getElementById(
            'section-' + sectionName
        );


        // Activate card
        if (selectedCard) {
            selectedCard.classList.add('active');
        }


        // Show section
        if (selectedSection) {
            selectedSection.classList.add('active');
        }

    }


    /*
    ==========================================================
    MANAGEMENT CARD CLICK
    ==========================================================
    */

    cards.forEach(function (card) {

        card.addEventListener('click', function () {

            const selectedSection =
                this.getAttribute('data-section');


            showSection(selectedSection);

        });

    });


    /*
    ==========================================================
    RESTORE SCHEDULE TAB AFTER DATE FILTER
    ==========================================================
    */

    const urlParams = new URLSearchParams(
        window.location.search
    );


    const selectedScheduleDate =
        urlParams.get('schedule_date');


    /*
    If a schedule date exists in the URL,
    automatically open the Schedule section.
    */

    if (selectedScheduleDate) {

        showSection('schedule');

    }

});

</script>


</body>

</html>