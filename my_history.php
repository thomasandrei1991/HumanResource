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
    // EMPLOYEE CHECK
    // ==========================================================

    if (($_SESSION['role'] ?? '') !== 'Employee') {
        header("Location: dashboard.php");
        exit();
    }

    // ==========================================================
    // GET SESSION DATA
    // ==========================================================

    $fullname = $_SESSION['fullname'] ?? 'Employee';
    $employeeId = $_SESSION['employee_id'] ?? 0;


    // ==========================================================
    // GET EMPLOYEE ATTENDANCE HISTORY
    // ==========================================================

    $historyResult = null;

    $historyQuery = mysqli_prepare($conn, "SELECT attendance_date, time_in, time_out, status
        FROM attendance
        WHERE employee_id = ?
        ORDER BY attendance_date DESC"
    );

    if ($historyQuery) {
        mysqli_stmt_bind_param($historyQuery, "i", $employeeId);
        mysqli_stmt_execute($historyQuery);
        $historyResult = mysqli_stmt_get_result($historyQuery);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/employee_dashboard.css">
    <title>My Attendance | HR Dashboard</title>
</head>

<body class="dashboard-page">
<div class="dashboard-shell">
    <?php include 'sidebar.php'; ?>
    <main class="dashboard-main">
        <div class="dashboard-container">

            <!-- ==================================================
                 PAGE HEADER
            ================================================== -->

            <div class="page-header">
                <div>
                    <p class="page-kicker">Employee Portal</p>
                    <h1>My Attendance</h1>
                </div>
            </div>

            <!-- ==================================================
                 ATTENDANCE HISTORY
            ================================================== -->

            <div class="employee-dashboard-panel">
                <div class="panel-header">
                    <h2>My Attendance History</h2>
                </div>
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
                        <?php if ($historyResult && mysqli_num_rows($historyResult) > 0): ?>
                            <?php while ($attendance = mysqli_fetch_assoc($historyResult)): ?>
                                <tr>
                                    <!-- DATE -->
                                    <td>
                                        <?php
                                            echo date('M d, Y', strtotime($attendance['attendance_date']));
                                        ?>
                                    </td>

                                    <!-- TIME IN -->
                                    <td>
                                        <?php
                                            echo !empty($attendance['time_in']) ? date('h:i A', strtotime($attendance['time_in'])): '--';
                                        ?>
                                    </td>

                                    <!-- TIME OUT -->
                                    <td>
                                        <?php
                                            echo !empty($attendance['time_out'])? date('h:i A', strtotime($attendance['time_out'])): '--';
                                        ?>
                                    </td>

                                    <!-- STATUS -->
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
                                            <?php
                                                echo htmlspecialchars($status);
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
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
        </div>
    </main>
</div>
</body>
</html>