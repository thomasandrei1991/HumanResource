<?php

    session_start();

    // ==========================
    // LOGIN CHECK
    // ==========================
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // ==========================
    // EMPLOYEE CHECK
    // ==========================
    if (($_SESSION['role'] ?? '') !== 'Employee') {
        header("Location: dashboard.php");
        exit();
    }

    // ==========================
    // GET SESSION DATA
    // ==========================
    $fullname   = $_SESSION['fullname'] ?? 'Employee';
    $employeeId = $_SESSION['employee_id'] ?? 0;

    // ==========================
    // DATABASE
    // ==========================
    require_once 'database.php';

    // ==========================
    // GET TODAY'S ATTENDANCE
    // ==========================
    $todayAttendance = null;

    $attendanceQuery = mysqli_prepare(
        $conn,
        "SELECT time_in, time_out, status
        FROM attendance
        WHERE employee_id = ?
        AND attendance_date = CURDATE()
        LIMIT 1"
    );

    mysqli_stmt_bind_param($attendanceQuery, "i", $employeeId);
    mysqli_stmt_execute($attendanceQuery);
    $attendanceResult = mysqli_stmt_get_result($attendanceQuery);

    if (mysqli_num_rows($attendanceResult) > 0) {
        $todayAttendance = mysqli_fetch_assoc($attendanceResult);
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/employee_dashboard.css">
    <title>Employee Dashboard</title>
</head>
<body class="dashboard-page">
<div class="dashboard-shell">

    <?php include 'sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="dashboard-container">

            <!-- ========== PAGE HEADER ========== -->
            <div class="page-header">
                <div>
                    <p class="page-kicker">Employee Portal</p>
                    <h1>Welcome, <?php echo htmlspecialchars($fullname); ?></h1>
                </div>
            </div>

            <!-- ========== TODAY'S ATTENDANCE ========== -->
            <div class="employee-dashboard-panel">
                <div class="panel-header">
                    <h2>Today's Attendance</h2>
                </div>

                <!-- ATTENDANCE ACTIONS -->
                <div class="attendance-actions">
                    <?php if (!$todayAttendance): ?>

                        <!-- NOT YET TIMED IN -->
                        <button type="button" class="primary-btn" onclick="window.location.href='add_employee_attendance.php'">
                            🕐 Time In
                        </button>
                        <button type="button" class="primary-btn" disabled>
                            🕐 Time Out
                        </button>

                    <?php elseif (empty($todayAttendance['time_out'])): ?>

                        <!-- ALREADY TIMED IN -->
                        <button type="button" class="primary-btn" disabled>
                            ✓ Timed In
                        </button>
                        <button type="button" class="primary-btn" onclick="window.location.href='update_employee_attendance.php'">
                            🕐 Time Out
                        </button>

                    <?php else: ?>

                        <!-- COMPLETED -->
                        <button type="button" class="primary-btn" disabled>
                            ✓ Timed In
                        </button>
                        <button type="button" class="primary-btn" disabled>
                            ✓ Timed Out
                        </button>

                    <?php endif; ?>
                </div>

                <!-- ATTENDANCE STATUS -->
                <div class="attendance-status">

                    <p>
                        <strong>Status:</strong>
                        <?php if (!$todayAttendance): ?>
                            <span>Not yet timed in</span>
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($todayAttendance['status']); ?></span>
                        <?php endif; ?>
                    </p>

                    <p>
                        <strong>Time In:</strong>
                        <?php
                            if ($todayAttendance && !empty($todayAttendance['time_in'])) {
                                echo date('h:i A', strtotime($todayAttendance['time_in']));
                            } else {
                                echo '--';
                            }
                        ?>
                    </p>

                    <p>
                        <strong>Time Out:</strong>
                        <?php
                            if ($todayAttendance && !empty($todayAttendance['time_out'])) {
                                echo date('h:i A', strtotime($todayAttendance['time_out']));
                            } else {
                                echo '--';
                            }
                        ?>
                    </p>
                </div>
            </div>

            <!-- ========== EMPLOYEE INFORMATION ========== -->
            <div class="employee-dashboard-panel">
                <div class="panel-header">
                    <h2>My Account</h2>
                </div>

                <p><strong>Name:</strong> <?php echo htmlspecialchars($fullname); ?></p>
                <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($employeeId); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
            </div>

            <!-- ========== ATTENDANCE HISTORY ========== -->
            <!--
            <h2>My Attendance History</h2>
            <div class="employee-dashboard-panel">
                <div class="table-scroll">
                    <table class="dashboard-table employee-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // GET EMPLOYEE ATTENDANCE HISTORY
                                $historyQuery = mysqli_prepare($conn, "SELECT attendance_date, time_in, time_out, status
                                    FROM attendance
                                    WHERE employee_id = ?
                                    ORDER BY attendance_date DESC"
                                );

                                mysqli_stmt_bind_param($historyQuery, "i", $employeeId);
                                mysqli_stmt_execute($historyQuery);
                                $historyResult = mysqli_stmt_get_result($historyQuery);

                                if (mysqli_num_rows($historyResult) > 0):
                                    while ($attendance = mysqli_fetch_assoc($historyResult)):
                            ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($attendance['attendance_date'])); ?></td>

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
                                            $status = $attendance['status'];

                                            switch ($status) {
                                                case 'Present':
                                                    $statusClass = 'present';
                                                    break;
                                                case 'Absent':
                                                    $statusClass = 'absent';
                                                    break;
                                                case 'Pending':
                                                    $statusClass = 'pending';
                                                    break;
                                                default:
                                                    $statusClass = 'pending';
                                                    break;
                                            }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php
                                    endwhile;
                                else:
                            ?>
                                <tr>
                                    <td colspan="4" style="text-align: center;">
                                        No attendance records found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            -->                 
        </div>
    </main>
</div>
</body>
</html>