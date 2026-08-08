<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/leave_management.css">
    <title>Leave Management | HR Dashboard</title>
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
        ?>
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="attendance-container">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Employee Leave</p>
                            <h1>Leave Management</h1>
                        </div>
                        <button class="primary-btn">+ New Leave</button>
                    </div>
                    <!-- Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card orange">
                            <h3>Pending</h3>
                            <p>12</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Approved</h3>
                            <p>48</p>
                        </div>
                        <div class="summary-card red">
                            <h3>Rejected</h3>
                            <p>5</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p>17</p>
                        </div>
                    </div>
                    <!-- Attendance Table -->
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Leave Requests</h2>
                        </div>
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Leave Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">JD</div>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>Vacation Leave</td>
                                    <td>Aug 5, 2026</td>
                                    <td>Aug 9, 2026</td>
                                    <td>5</td>
                                    <td>
                                        <span class="status-badge pending">Pending
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar green-bg">JS</div>
                                            Jane Smith
                                        </div>
                                    </td>
                                    <td>Sick Leave</td>
                                    <td>Jul 30, 2026</td>
                                    <td>Jul 31, 2026</td>
                                    <td>2</td>
                                    <td>
                                        <span class="status-badge present">Approved</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar orange-bg">MR</div>
                                            Mark Reyes
                                        </div>
                                    </td>
                                    <td>Emergency Leave</td>
                                    <td>Jul 28, 2026</td>
                                    <td>Jul 28, 2026</td>
                                    <td>1</td>
                                    <td>
                                        <span class="status-badge absent">
                                            Rejected
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
