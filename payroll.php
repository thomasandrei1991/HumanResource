<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/payroll.css">
    <title>Payroll | HR Dashboard</title>
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
                            <p class="page-kicker">Salary Management</p>
                            <h1>Payroll</h1>
                        </div>
                        <button class="primary-btn">
                            + Generate Payroll
                        </button>
                    </div>
                    <!-- Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Employees</h3>
                            <p>248</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Paid This Month</h3>
                            <p>231</p>
                        </div>
                        <div class="summary-card orange">
                            <h3>Pending</h3>
                            <p>17</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>Total Payroll</h3>
                            <p>₱1.85M</p>
                        </div>
                    </div>
                    <!-- Payroll Table -->
                    <div class="employee-panel">
                <div class="panel-header">
                    <h2>Payroll Records</h2>
                </div>
                <table class="dashboard-table employee-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Basic Salary</th>
                            <th>Overtime</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
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
                            <td>₱30,000</td>
                            <td>₱2,500</td>
                            <td>₱1,200</td>
                            <td><strong>₱31,300</strong></td>
                            <td>
                                <span class="status-badge present">
                                    Paid
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
                            <td>₱28,000</td>
                            <td>₱1,000</td>
                            <td>₱900</td>
                            <td><strong>₱28,100</strong></td>
                            <td>
                                <span class="status-badge pending">
                                    Pending
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="employee-name">
                                    <div class="emp-avatar orange-bg">MR</div>
                                    Mark Reyes
                                </div>
                            </td>
                            <td>₱35,000</td>
                            <td>₱3,000</td>
                            <td>₱1,500</td>
                            <td><strong>₱36,500</strong></td>
                            <td>
                                <span class="status-badge present">
                                    Paid
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
