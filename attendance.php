<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/attendance.css">
    <title>Attendance | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="attendance-container">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Time & Attendance</p>
                            <h1>Attendance</h1>
                        </div>
                        <button class="primary-btn">
                            + Record Attendance
                        </button>
                    </div>
                    <!-- Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Present Today</h3>
                            <p>214</p>
                        </div>
                        <div class="summary-card orange">
                            <h3>Late</h3>
                            <p>12</p>
                        </div>
                        <div class="summary-card red">
                            <h3>Absent</h3>
                            <p>8</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p>14</p>
                        </div>
                    </div>
                    <!-- Attendance Table -->
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Today's Attendance</h2>
                        </div>
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">
                                                JD
                                            </div>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>IT</td>
                                    <td>July 31, 2026</td>
                                    <td>8:00 AM</td>
                                    <td>5:00 PM</td>
                                    <td>
                                        <span class="status-badge present">
                                            Present
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar green-bg">
                                                JS
                                            </div>
                                            Jane Smith
                                        </div>
                                    </td>
                                    <td>HR</td>
                                    <td>July 31, 2026</td>
                                    <td>8:15 AM</td>
                                    <td>5:00 PM</td>
                                    <td>
                                        <span class="status-badge late">
                                            Late
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar orange-bg">
                                                MR
                                            </div>
                                            Mark Reyes
                                        </div>
                                    </td>
                                    <td>Finance</td>
                                    <td>July 31, 2026</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <span class="status-badge absent">
                                            Absent
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar purple-bg">
                                                KL
                                            </div>
                                            Kate Lopez
                                        </div>
                                    </td>
                                    <td>Marketing</td>
                                    <td>July 31, 2026</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <span class="status-badge on-leave">
                                            On Leave
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
