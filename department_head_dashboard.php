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
// ROLE CHECK
// ==========================================================

if (($_SESSION['role'] ?? '') !== 'Department Head') {

    header("Location: dashboard.php");
    exit();

}


// ==========================================================
// CURRENT DEPARTMENT HEAD
// ==========================================================

$fullname = $_SESSION['fullname'] ?? 'Department Head';

$employeeId = intval($_SESSION['employee_id'] ?? 0);

$departmentName = '';


// ==========================================================
// GET DEPARTMENT HEAD'S DEPARTMENT
// ==========================================================

$departmentQuery = mysqli_prepare(
    $conn,

    "SELECT
        department_name
     FROM departments
     WHERE LOWER(department_head) = LOWER(?)
     AND LOWER(status) = 'active'
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $departmentQuery,
    "s",
    $fullname
);

mysqli_stmt_execute(
    $departmentQuery
);

$departmentResult = mysqli_stmt_get_result(
    $departmentQuery
);


if (mysqli_num_rows($departmentResult) > 0) {

    $departmentData = mysqli_fetch_assoc(
        $departmentResult
    );

    $departmentName = $departmentData['department_name'] ?? '';

}


// ==========================================================
// DEFAULT VALUES
// ==========================================================

$totalEmployees = 0;
$activeEmployees = 0;
$onLeaveEmployees = 0;

$presentToday = 0;
$lateToday = 0;
$absentToday = 0;

$pendingLeaves = 0;


// ==========================================================
// EMPLOYEE COUNTS
// ==========================================================

if ($departmentName !== '') {

    // ------------------------------------------------------
    // TOTAL EMPLOYEES
    // ------------------------------------------------------

    $totalQuery = mysqli_prepare(
        $conn,

        "SELECT COUNT(*) AS total
         FROM employees
         WHERE LOWER(department) = LOWER(?)"
    );

    mysqli_stmt_bind_param(
        $totalQuery,
        "s",
        $departmentName
    );

    mysqli_stmt_execute($totalQuery);

    $totalResult = mysqli_stmt_get_result(
        $totalQuery
    );

    $totalData = mysqli_fetch_assoc(
        $totalResult
    );

    $totalEmployees = intval(
        $totalData['total'] ?? 0
    );


    // ------------------------------------------------------
    // ACTIVE EMPLOYEES
    // ------------------------------------------------------

    $activeQuery = mysqli_prepare(
        $conn,

        "SELECT COUNT(*) AS total
         FROM employees
         WHERE LOWER(department) = LOWER(?)
         AND LOWER(employment_status) = 'active'"
    );

    mysqli_stmt_bind_param(
        $activeQuery,
        "s",
        $departmentName
    );

    mysqli_stmt_execute($activeQuery);

    $activeResult = mysqli_stmt_get_result(
        $activeQuery
    );

    $activeData = mysqli_fetch_assoc(
        $activeResult
    );

    $activeEmployees = intval(
        $activeData['total'] ?? 0
    );


    // ------------------------------------------------------
    // ON LEAVE EMPLOYEES
    // ------------------------------------------------------

    $onLeaveQuery = mysqli_prepare(
        $conn,

        "SELECT COUNT(*) AS total
         FROM employees
         WHERE LOWER(department) = LOWER(?)
         AND LOWER(employment_status) = 'on leave'"
    );

    mysqli_stmt_bind_param(
        $onLeaveQuery,
        "s",
        $departmentName
    );

    mysqli_stmt_execute($onLeaveQuery);

    $onLeaveResult = mysqli_stmt_get_result(
        $onLeaveQuery
    );

    $onLeaveData = mysqli_fetch_assoc(
        $onLeaveResult
    );

    $onLeaveEmployees = intval(
        $onLeaveData['total'] ?? 0
    );

}


// ==========================================================
// TODAY'S ATTENDANCE
// ==========================================================

if ($departmentName !== '') {


    // ------------------------------------------------------
    // PRESENT TODAY
    // ------------------------------------------------------

    $presentQuery = mysqli_prepare(
        $conn,

        "SELECT COUNT(*) AS total
         FROM attendance a

         INNER JOIN employees e
             ON a.employee_id = e.id

         WHERE LOWER(e.department) = LOWER(?)

         AND a.attendance_date = CURDATE()

         AND LOWER(a.status) = 'present'"
    );

    mysqli_stmt_bind_param(
        $presentQuery,
        "s",
        $departmentName
    );

    mysqli_stmt_execute($presentQuery);

    $presentResult = mysqli_stmt_get_result(
        $presentQuery
    );

    $presentData = mysqli_fetch_assoc(
        $presentResult
    );

    $presentToday = intval(
        $presentData['total'] ?? 0
    );


    // ------------------------------------------------------
    // LATE TODAY
    // ------------------------------------------------------

    $lateQuery = mysqli_prepare(
        $conn,

        "SELECT COUNT(*) AS total
         FROM attendance a

         INNER JOIN employees e
             ON a.employee_id = e.id

         WHERE LOWER(e.department) = LOWER(?)

         AND a.attendance_date = CURDATE()

         AND LOWER(a.status) = 'late'"
    );

    mysqli_stmt_bind_param(
        $lateQuery,
        "s",
        $departmentName
    );

    mysqli_stmt_execute($lateQuery);

    $lateResult = mysqli_stmt_get_result(
        $lateQuery
    );

    $lateData = mysqli_fetch_assoc(
        $lateResult
    );

    $lateToday = intval(
        $lateData['total'] ?? 0
    );


    // ------------------------------------------------------
    // ABSENT TODAY
    // ------------------------------------------------------

    $absentQuery = mysqli_prepare(
        $conn,

        "SELECT COUNT(*) AS total
         FROM employees e

         WHERE LOWER(e.department) = LOWER(?)

         AND e.id NOT IN (

             SELECT a.employee_id
             FROM attendance a
             WHERE a.attendance_date = CURDATE()

         )"
    );

    mysqli_stmt_bind_param(
        $absentQuery,
        "s",
        $departmentName
    );

    mysqli_stmt_execute($absentQuery);

    $absentResult = mysqli_stmt_get_result(
        $absentQuery
    );

    $absentData = mysqli_fetch_assoc(
        $absentResult
    );

    $absentToday = intval(
        $absentData['total'] ?? 0
    );

}


// ==========================================================
// PENDING LEAVE REQUESTS
// ==========================================================

if ($departmentName !== '') {

    $leaveQuery = mysqli_prepare(
        $conn,

        "SELECT COUNT(*) AS total

         FROM leave_requests l

         INNER JOIN employees e
             ON l.employee_id = e.id

         WHERE LOWER(e.department) = LOWER(?)

         AND LOWER(l.status) = 'pending'"
    );

    mysqli_stmt_bind_param(
        $leaveQuery,
        "s",
        $departmentName
    );

    mysqli_stmt_execute($leaveQuery);

    $leaveResult = mysqli_stmt_get_result(
        $leaveQuery
    );

    $leaveData = mysqli_fetch_assoc(
        $leaveResult
    );

    $pendingLeaves = intval(
        $leaveData['total'] ?? 0
    );

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

    <title>Department Head Dashboard</title>

</head>


<body class="dashboard-page">

<div class="dashboard-shell">


    <?php include 'sidebar.php'; ?>


    <main class="dashboard-main">

        <div class="dashboard-container">


            <!-- ==========================
                 PAGE HEADER
            =========================== -->

            <div class="page-header">

                <div>

                    <p class="page-kicker">
                        Department Head Portal
                    </p>

                    <h1>
                        Welcome,
                        <?php echo htmlspecialchars($fullname); ?>
                    </h1>

                    <p>

                        Department:

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $departmentName !== ''
                                    ? $departmentName
                                    : 'Not Assigned'
                            );

                            ?>

                        </strong>

                    </p>

                </div>

            </div>


            <!-- ==========================
                 SUMMARY CARDS
            =========================== -->

            <div class="employee-summary">


                <!-- TOTAL EMPLOYEES -->

                <div class="summary-card blue">

                    <h3>
                        <?php echo $totalEmployees; ?>
                    </h3>

                    <p>
                        Total Employees
                    </p>

                </div>


                <!-- ACTIVE EMPLOYEES -->

                <div class="summary-card green">

                    <h3>
                        <?php echo $activeEmployees; ?>
                    </h3>

                    <p>
                        Active Employees
                    </p>

                </div>


                <!-- LATE TODAY -->

                <div class="summary-card orange">

                    <h3>
                        <?php echo $lateToday; ?>
                    </h3>

                    <p>
                        Late Today
                    </p>

                </div>


                <!-- ON LEAVE -->

                <div class="summary-card purple">

                    <h3>
                        <?php echo $onLeaveEmployees; ?>
                    </h3>

                    <p>
                        On Leave
                    </p>

                </div>


            </div>


            <!-- ==========================
                 QUICK ACTIONS
            =========================== -->

            <div class="employee-panel">

                <div class="panel-header">

                    <h2>
                        Department Management
                    </h2>

                </div>


                <div class="attendance-actions">


                    <a
                        href="employee.php"
                        class="primary-btn"
                    >
                        👥 Employees
                    </a>


                    <a
                        href="attendance.php"
                        class="primary-btn"
                    >
                        🕐 Attendance
                    </a>


                    <a
                        href="leave_management.php"
                        class="primary-btn"
                    >
                        📅 Leave Requests
                    </a>


                    <a
                        href="schedule.php"
                        class="primary-btn"
                    >
                        📋 Schedule
                    </a>


                </div>

            </div>


            <!-- ==========================
                 TODAY'S ATTENDANCE
            =========================== -->

            <div class="employee-panel">

                <div class="panel-header">

                    <h2>
                        Today's Attendance
                    </h2>

                </div>


                <p>

                    <strong>
                        Present:
                    </strong>

                    <?php echo $presentToday; ?>

                </p>


                <p>

                    <strong>
                        Late:
                    </strong>

                    <?php echo $lateToday; ?>

                </p>


                <p>

                    <strong>
                        Absent:
                    </strong>

                    <?php echo $absentToday; ?>

                </p>


                <p>

                    <strong>
                        Pending Leave Requests:
                    </strong>

                    <?php echo $pendingLeaves; ?>

                </p>

            </div>


            <!-- ==========================
                 DEPARTMENT INFORMATION
            =========================== -->

            <div class="employee-panel">

                <div class="panel-header">

                    <h2>
                        Department Information
                    </h2>

                </div>


                <p>

                    <strong>
                        Department Head:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $fullname
                    );

                    ?>

                </p>


                <p>

                    <strong>
                        Department:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $departmentName !== ''
                            ? $departmentName
                            : 'Not Assigned'
                    );

                    ?>

                </p>


                <p>

                    <strong>
                        Employee ID:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $employeeId
                    );

                    ?>

                </p>


                <p>

                    <strong>
                        Role:
                    </strong>

                    <?php

                    echo htmlspecialchars(
                        $_SESSION['role']
                    );

                    ?>

                </p>

            </div>


        </div>

    </main>

</div>

</body>

</html>