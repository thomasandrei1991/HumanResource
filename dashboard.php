<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="container">
        <div class="form-container dashboard-container">
            <!-- Header -->
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
                    <a href="login.php" class="logout-btn">Sign Out</a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        👥
                    </div>
                    <div class="stat-info">
                        <h3>Total Employees</h3>
                        <div class="stat-number">248</div>
                        <div class="stat-change positive">↑ 12% this month</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        ✅
                    </div>
                    <div class="stat-info">
                        <h3>Present Today</h3>
                        <div class="stat-number">214</div>
                        <div class="stat-change positive">↑ 86% attendance</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        📋
                    </div>
                    <div class="stat-info">
                        <h3>Leave Requests</h3>
                        <div class="stat-number">12</div>
                        <div class="stat-change negative">↑ 3 pending</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">
                        ⏳
                    </div>
                    <div class="stat-info">
                        <h3>On Leave</h3>
                        <div class="stat-number">18</div>
                        <div class="stat-change positive">↓ 5 from last week</div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Recent Attendance Table -->
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
                            <tr>
                                <td>
                                    <div class="employee-name">
                                        <div class="emp-avatar blue-bg">EW</div>
                                        Emily Watson
                                    </div>
                                </td>
                                <td>HR</td>
                                <td>—</td>
                                <td><span class="status-badge absent">Absent</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-panel">
                    <div class="panel-header">
                        <h2>Recent Activity</h2>
                        <a href="#" class="view-all">View All →</a>
                    </div>
                    <ul class="activity-list">
                        <li>
                            <div class="activity-dot green"></div>
                            <div>
                                <div class="activity-text">
                                    <strong>Sarah Martinez</strong> marked attendance
                                </div>
                                <div class="activity-time">5 min ago</div>
                            </div>
                        </li>
                        <li>
                            <div class="activity-dot orange"></div>
                            <div>
                                <div class="activity-text">
                                    <strong>Leave request</strong> from Rachel Kim (PTO)
                                </div>
                                <div class="activity-time">25 min ago</div>
                            </div>
                        </li>
                        <li>
                            <div class="activity-dot blue"></div>
                            <div>
                                <div class="activity-text">
                                    <strong>New employee</strong> Michael Chen joined Engineering
                                </div>
                                <div class="activity-time">1 hour ago</div>
                            </div>
                        </li>
                        <li>
                            <div class="activity-dot red"></div>
                            <div>
                                <div class="activity-text">
                                    <strong>Overtime</strong> approved for Design Team
                                </div>
                                <div class="activity-time">2 hours ago</div>
                            </div>
                        </li>
                        <li>
                            <div class="activity-dot green"></div>
                            <div>
                                <div class="activity-text">
                                    <strong>Payroll</strong> for September processed successfully
                                </div>
                                <div class="activity-time">3 hours ago</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

