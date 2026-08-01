<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Performance | HR Dashboard</title>
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
                            <p class="page-kicker">Employee Evaluation</p>
                            <h1>Performance</h1>
                        </div>
                        <button class="primary-btn">
                            + New Evaluation
                        </button>
                    </div>
                    <!-- Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Top Performers</h3>
                            <p>24</p>
                        </div>
                        <div class="summary-card orange">
                            <h3>Average Rating</h3>
                            <p>4.6</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Needs Improvement</h3>
                            <p>8</p>
                        </div>
                        <div class="summary-card red">
                            <h3>Evaluation</h3>
                            <p>248</p>
                        </div>
                    </div>
                    <!-- Payroll Table -->
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Applicant List</h2>

                            <a href="#" class="view-all">
                                Export →
                            </a>
                        </div>
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Rating</th>
                                    <th>Evaluation date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">JM</div>
                                            John Miller
                                        </div>
                                    </td>
                                    <td>Information Technology</td>
                                    <td>⭐⭐⭐⭐⭐</td>
                                    <td>August 2, 2026</td>
                                    <td>
                                        <span class="status-badge pending">
                                            Excellent
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">BG</div>
                                            Boy George
                                        </div>
                                    </td>
                                    <td>Human Resource</td>
                                    <td>⭐⭐⭐⭐</td>
                                    <td>August 2, 2026</td>
                                    <td>
                                        <span class="status-badge pending">
                                            Good
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">JC</div>
                                            Jack Cole
                                        </div>
                                    </td>
                                    <td>Marketing</td>
                                    <td>⭐⭐⭐</td>
                                    <td>August 2, 2026</td>
                                    <td>
                                        <span class="status-badge pending">
                                            Average
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
