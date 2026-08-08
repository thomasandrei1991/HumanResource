<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/reports.css">
    <title>Reports | HR Dashboard</title>
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
                <div class="employee-container">
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Business Intelligence</p>
                            <h1>Reports</h1>
                        </div>
                        <button class="primary-btn">Export Report</button>
                    </div>

                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Attendance Rate</h3>
                            <p>94.6%</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Leave Requests</h3>
                            <p>24</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>Open Positions</h3>
                            <p>7</p>
                        </div>
                        <div class="summary-card orange">
                            <h3>Pending Payroll</h3>
                            <p>12</p>
                        </div>
                    </div>

                    <div class="settings-grid">
                        <div class="employee-panel">
                            <div class="panel-header">
                                <h2>HR Performance Snapshot</h2>
                                <a href="#" class="view-all">View Details →</a>
                            </div>
                            <table class="dashboard-table employee-table">
                                <thead>
                                    <tr>
                                        <th>Metric</th>
                                        <th>Current</th>
                                        <th>Target</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Employee Retention</td>
                                        <td>91%</td>
                                        <td>93%</td>
                                        <td><span class="status-badge pending">Near Target</span></td>
                                    </tr>
                                    <tr>
                                        <td>Recruitment Cycle</td>
                                        <td>14 days</td>
                                        <td>10 days</td>
                                        <td><span class="status-badge absent">Needs Review</span></td>
                                    </tr>
                                    <tr>
                                        <td>Payroll Accuracy</td>
                                        <td>99.2%</td>
                                        <td>99.5%</td>
                                        <td><span class="status-badge present">On Track</span></td>
                                    </tr>
                                    <tr>
                                        <td>Training Completion</td>
                                        <td>78%</td>
                                        <td>85%</td>
                                        <td><span class="status-badge late">Improving</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="employee-panel">
                            <div class="panel-header">
                                <h2>Report Categories</h2>
                            </div>
                            <div class="settings-list">
                                <div class="setting-item">
                                    <div>
                                        <strong>Attendance Report</strong>
                                        <p>Daily, weekly, and monthly attendance summaries.</p>
                                    </div>
                                    <span class="setting-pill">Live</span>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Payroll Report</strong>
                                        <p>Salary, deductions, and overtime breakdowns.</p>
                                    </div>
                                    <span class="setting-pill">Ready</span>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Recruitment Report</strong>
                                        <p>Hiring pipeline and candidate progress overview.</p>
                                    </div>
                                    <span class="setting-pill">Updated</span>
                                </div>
                                <div class="setting-item">
                                    <div>
                                        <strong>Performance Report</strong>
                                        <p>Evaluation progress and employee development insights.</p>
                                    </div>
                                    <span class="setting-pill">Draft</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
