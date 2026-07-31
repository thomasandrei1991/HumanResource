<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Employees | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="dashboard-container">
                <div class="employee-container">
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">People Management</p>
                            <h1>Employees</h1>
                        </div>
                        <button class="primary-btn">+ Add Employee</button>
                    </div>
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Total Staff</h3>
                            <p>248</p>
                        </div>
                        <div class="summary-card green">
                            <h3>Active</h3>
                            <p>231</p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p>17</p>
                        </div>
                    </div>
                    <div class="employee-panel">
                        <div class="panel-header">
                            <h2>Employee Directory</h2>
                            <a href="#" class="view-all">Export →</a>
                        </div>

                        <table class="dashboard-table employee-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar blue-bg">SM</div>
                                            Sarah Martinez
                                        </div>
                                    </td>
                                    <td>Engineering</td>
                                    <td>Software Engineer</td>
                                    <td><span class="status-badge present">Active</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar green-bg">AJ</div>
                                            Alex Johnson
                                        </div>
                                    </td>
                                    <td>Marketing</td>
                                    <td>Marketing Lead</td>
                                    <td><span class="status-badge present">Active</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar orange-bg">RK</div>
                                            Rachel Kim
                                        </div>
                                    </td>
                                    <td>Design</td>
                                    <td>UI/UX Designer</td>
                                    <td><span class="status-badge on-leave">On Leave</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="employee-name">
                                            <div class="emp-avatar purple-bg">MT</div>
                                            Marcus Thompson
                                        </div>
                                    </td>
                                    <td>Finance</td>
                                    <td>Accountant</td>
                                    <td><span class="status-badge late">Pending</span></td>
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
