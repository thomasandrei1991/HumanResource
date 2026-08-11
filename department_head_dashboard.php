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
    "SELECT department_name
     FROM departments
     WHERE LOWER(department_head) = LOWER(?)
     AND LOWER(status) = 'active'
     LIMIT 1"
);

mysqli_stmt_bind_param($departmentQuery, "s", $fullname);
mysqli_stmt_execute($departmentQuery);
$departmentResult = mysqli_stmt_get_result($departmentQuery);

if (mysqli_num_rows($departmentResult) > 0) {
    $departmentData = mysqli_fetch_assoc($departmentResult);
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
    // TOTAL EMPLOYEES
    $totalQuery = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM employees
         WHERE LOWER(department) = LOWER(?)"
    );
    mysqli_stmt_bind_param($totalQuery, "s", $departmentName);
    mysqli_stmt_execute($totalQuery);
    $totalResult = mysqli_stmt_get_result($totalQuery);
    $totalData = mysqli_fetch_assoc($totalResult);
    $totalEmployees = intval($totalData['total'] ?? 0);

    // ACTIVE EMPLOYEES
    $activeQuery = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM employees
         WHERE LOWER(department) = LOWER(?)
         AND LOWER(employment_status) = 'active'"
    );
    mysqli_stmt_bind_param($activeQuery, "s", $departmentName);
    mysqli_stmt_execute($activeQuery);
    $activeResult = mysqli_stmt_get_result($activeQuery);
    $activeData = mysqli_fetch_assoc($activeResult);
    $activeEmployees = intval($activeData['total'] ?? 0);

    // ON LEAVE EMPLOYEES
    $onLeaveQuery = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM employees
         WHERE LOWER(department) = LOWER(?)
         AND LOWER(employment_status) = 'on leave'"
    );
    mysqli_stmt_bind_param($onLeaveQuery, "s", $departmentName);
    mysqli_stmt_execute($onLeaveQuery);
    $onLeaveResult = mysqli_stmt_get_result($onLeaveQuery);
    $onLeaveData = mysqli_fetch_assoc($onLeaveResult);
    $onLeaveEmployees = intval($onLeaveData['total'] ?? 0);
}

// ==========================================================
// TODAY'S ATTENDANCE
// ==========================================================
if ($departmentName !== '') {
    // PRESENT TODAY
    $presentQuery = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM attendance a
         INNER JOIN employees e ON a.employee_id = e.id
         WHERE LOWER(e.department) = LOWER(?)
         AND a.attendance_date = CURDATE()
         AND LOWER(a.status) = 'present'"
    );
    mysqli_stmt_bind_param($presentQuery, "s", $departmentName);
    mysqli_stmt_execute($presentQuery);
    $presentResult = mysqli_stmt_get_result($presentQuery);
    $presentData = mysqli_fetch_assoc($presentResult);
    $presentToday = intval($presentData['total'] ?? 0);

    // LATE TODAY
    $lateQuery = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM attendance a
         INNER JOIN employees e ON a.employee_id = e.id
         WHERE LOWER(e.department) = LOWER(?)
         AND a.attendance_date = CURDATE()
         AND LOWER(a.status) = 'late'"
    );
    mysqli_stmt_bind_param($lateQuery, "s", $departmentName);
    mysqli_stmt_execute($lateQuery);
    $lateResult = mysqli_stmt_get_result($lateQuery);
    $lateData = mysqli_fetch_assoc($lateResult);
    $lateToday = intval($lateData['total'] ?? 0);

    // ABSENT TODAY
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
    mysqli_stmt_bind_param($absentQuery, "s", $departmentName);
    mysqli_stmt_execute($absentQuery);
    $absentResult = mysqli_stmt_get_result($absentQuery);
    $absentData = mysqli_fetch_assoc($absentResult);
    $absentToday = intval($absentData['total'] ?? 0);
}

// ==========================================================
// PENDING LEAVE REQUESTS
// ==========================================================
if ($departmentName !== '') {
    $leaveQuery = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM leave_requests l
         INNER JOIN employees e ON l.employee_id = e.id
         WHERE LOWER(e.department) = LOWER(?)
         AND LOWER(l.status) = 'pending'"
    );
    mysqli_stmt_bind_param($leaveQuery, "s", $departmentName);
    mysqli_stmt_execute($leaveQuery);
    $leaveResult = mysqli_stmt_get_result($leaveQuery);
    $leaveData = mysqli_fetch_assoc($leaveResult);
    $pendingLeaves = intval($leaveData['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <title>Department Head Dashboard</title>

    <style>
        /* Base reset & layout fixes */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body.dashboard-page {
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f6f9;
            color: #1e293b;
        }

        /* Prevents overlapping between sidebar and main area */
        .dashboard-shell {
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow-x: auto;
        }

        .dashboard-main {
            flex: 1;
            min-width: 0;           /* Prevents flex child from exceeding viewport */
            width: 100%;            /* Spans full available space next to sidebar */
            padding: 2rem;
            background-color: #f8fafc;
                  /* Handles vertical scrolling smoothly */
        }

        .dashboard-container {
            width: 100%;            /* Forces container to expand to full width of .dashboard-main */
            max-width: 1200px;      /* Sets the upper size limit */
            margin: 0 auto;         /* Keeps it centered */
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            box-sizing: border-box; /* Includes padding within the container's calculated width */
            overflow-x: auto;
        }

        /* Header block */
        .page-header {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .page-kicker {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0 0 0.25rem 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .page-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 1.75rem;
            color: #0f172a;
        }

        .page-header p {
            margin: 0;
            color: #475569;
        }

        /* Summary Cards Responsive Grid */
        .employee-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
        }

        .summary-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;            /* Spacing between H3 title and P counter */
            padding: 22px 24px;
            min-height: 130px;
            border-radius: var(--radius-lg, 16px);
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Smooth Hover Lift Effect */
        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        /* Gradient Background Themes */
        .summary-card.blue {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .summary-card.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .summary-card.purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        }

        .summary-card.orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .summary-card.red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        /* Translucent Background Circle Overlay */
        .summary-card::after {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }

        /* General Panel Layout */
        .employee-panel {
            background: #ffffff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .panel-header {
            margin-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.75rem;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #0f172a;
        }

        /* FIX: Action Buttons Gap & Alignment */
        .attendance-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.875rem; /* Generates equal spacing between all buttons */
            align-items: center;
        }

        .primary-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.625rem 1.25rem;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.925rem;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .primary-btn:hover {
            background-color: #1d4ed8;
        }

        .employee-panel p {
            margin: 0.5rem 0;
            color: #334155;
            font-size: 0.95rem;
        }

       
    </style>
</head>

<body class="dashboard-page">
<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="dashboard-container">

            <!-- PAGE HEADER -->
            <div class="page-header">
                <div>
                    <p class="page-kicker">Department Head Portal</p>
                    <h1>Welcome, <?php echo htmlspecialchars($fullname); ?></h1>
                    <p>
                        Department: 
                        <strong>
                            <?php echo htmlspecialchars($departmentName !== '' ? $departmentName : 'Not Assigned'); ?>
                        </strong>
                    </p>
                </div>
            </div>

            <!-- SUMMARY CARDS -->
            <div class="employee-summary">
                <div class="summary-card blue">
                    <h2>Total Employees</h2>
                    <p><?php echo $totalEmployees; ?></p>
                </div>

                <div class="summary-card green">
                    <h2>Active Employees</h2>
                    <p><?php echo $activeEmployees; ?></p>
                </div>

                <div class="summary-card orange">
                    <h2>Late Today</h2>
                    <p><?php echo $lateToday; ?></h3>
                </div>

                <div class="summary-card purple">
                    <h2>On Leave</h2>
                    <p><?php echo $onLeaveEmployees; ?></p>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="employee-panel">
                <div class="panel-header">
                    <h2>Department Management</h2>
                </div>
                <div class="attendance-actions">
                    <a href="employee.php" class="primary-btn">👥 Employees</a>
                    <a href="attendance.php" class="primary-btn">🕐 Attendance</a>
                    <a href="leave_management.php" class="primary-btn">📅 Leave Requests</a>
                    <a href="schedule.php" class="primary-btn">📋 Schedule</a>
                </div>
            </div>

            <!-- TODAY'S ATTENDANCE -->
            <div class="employee-panel">
                <div class="panel-header">
                    <h2>Today's Attendance</h2>
                </div>
                <p><strong>Present:</strong> <?php echo $presentToday; ?></p>
                <p><strong>Late:</strong> <?php echo $lateToday; ?></p>
                <p><strong>Absent:</strong> <?php echo $absentToday; ?></p>
                <p><strong>Pending Leave Requests:</strong> <?php echo $pendingLeaves; ?></p>
            </div>

            <!-- DEPARTMENT INFORMATION -->
            <div class="employee-panel">
                <div class="panel-header">
                    <h2>Department Information</h2>
                </div>
                <p><strong>Department Head:</strong> <?php echo htmlspecialchars($fullname); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($departmentName !== '' ? $departmentName : 'Not Assigned'); ?></p>
                <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($employeeId); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
            </div>

        </div>
    </main>

</div>
</body>
</html>