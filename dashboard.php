<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>
        <main class="dashboard-main">
            <div class="view-panel active" id="dashboardView">
                <div class="dashboard-container">
                    <div class="dashboard-header">
                        <div class="welcome">
                            <h1>Welcome back, John! 👋</h1>
                            <p>Here's what's happening with your team today.</p>
                        </div>
                        <div class="user-profile">
                            <div class="user-info">
                                <span>John Doe</span>
                                <span>HR Manager</span>
                            </div>
                            <div class="avatar">JD</div>
                        </div>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon blue">👥</div>
                            <div class="stat-info">
                                <h3>Total Employees</h3>
                                <div class="stat-number">248</div>
                                <div class="stat-change positive">↑ 12% this month</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon green">✅</div>
                            <div class="stat-info">
                                <h3>Present Today</h3>
                                <div class="stat-number">214</div>
                                <div class="stat-change positive">↑ 86% attendance</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon orange">📋</div>
                            <div class="stat-info">
                                <h3>Leave Requests</h3>
                                <div class="stat-number">12</div>
                                <div class="stat-change negative">↑ 3 pending</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon purple">⏳</div>
                            <div class="stat-info">
                                <h3>On Leave</h3>
                                <div class="stat-number">18</div>
                                <div class="stat-change positive">↓ 5 from last week</div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-content">
                        <div class="dashboard-panel">
                            <div class="panel-header">
                                <h2>Recent Attendance</h2>
                                <a href="#" class="view-all">View All →</a>
                            </div>
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Time In</th>
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
                                        <td>08:02 AM</td>
                                        <td><span class="status-badge present">Present</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar green-bg">AJ</div>
                                                Alex Johnson
                                            </div>
                                        </td>
                                        <td>Marketing</td>
                                        <td>08:15 AM</td>
                                        <td><span class="status-badge present">Present</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar orange-bg">RK</div>
                                                Rachel Kim
                                            </div>
                                        </td>
                                        <td>Design</td>
                                        <td>—</td>
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
                                        <td>09:30 AM</td>
                                        <td><span class="status-badge late">Late</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="dashboard-panel">
                            <div class="panel-header">
                                <h2>Recent Activity</h2>
                                <a href="#" class="view-all">View All →</a>
                            </div>
                            <ul class="activity-list">
                                <li>
                                    <div class="activity-dot green"></div>
                                    <div>
                                        <div class="activity-text"><strong>Sarah Martinez</strong> marked attendance</div>
                                        <div class="activity-time">5 min ago</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity-dot orange"></div>
                                    <div>
                                        <div class="activity-text"><strong>Leave request</strong> from Rachel Kim (PTO)</div>
                                        <div class="activity-time">25 min ago</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity-dot blue"></div>
                                    <div>
                                        <div class="activity-text"><strong>New employee</strong> Michael Chen joined Engineering</div>
                                        <div class="activity-time">1 hour ago</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity-dot red"></div>
                                    <div>
                                        <div class="activity-text"><strong>Overtime</strong> approved for Design Team</div>
                                        <div class="activity-time">2 hours ago</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="view-panel" id="employeeView">
                <div class="form-container dashboard-container">
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
                                            <div class="emp-avatar purple-bg">MT</div>
                                            Marcus Thompson
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
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>

