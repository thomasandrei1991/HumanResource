<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Departments | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="department-container">
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Organization Management</p>
                            <h1>Departments</h1>
                        </div>
                        <button class="primary-btn">+ Add Department</button>
                    </div>
                    <!-- Summary Cards -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Total Departments</h3>
                            <p>8</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Total Employees</h3>
                            <p>248</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>Department Heads</h3>
                            <p>8</p>
                        </div>
                    </div>
                    <!-- Department Table -->
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Department Directory</h2>
                            <a href="#" class="view-all">Export →</a>
                        </div>
                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Department Head</th>
                                    <th>Total Employees</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">IT</div>
                                            Information Technology
                                        </div>
                                    </td>
                                    <td>Sarah Martinez</td>
                                    <td>54</td>
                                    <td>
                                        <span class="status-badge present">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar green-bg">HR</div>
                                            Human Resources
                                        </div>
                                    </td>
                                    <td>Alex Johnson</td>
                                    <td>18</td>
                                    <td>
                                        <span class="status-badge present">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar orange-bg">FN</div>
                                            Finance
                                        </div>
                                    </td>
                                    <td>Rachel Kim</td>
                                    <td>26</td>
                                    <td>
                                        <span class="status-badge present">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar purple-bg">MK</div>
                                            Marketing
                                        </div>
                                    </td>
                                    <td>Marcus Thompson</td>
                                    <td>31</td>
                                    <td>
                                        <span class="status-badge present">Active</span>
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
