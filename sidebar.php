<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon">HR</div>
        <div>
            <h2>HR Portal</h2>
            <p>Human Resource</p>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>"><img src="images/chart-bar-popular.png" alt=""> Dashboard</a>
        <a href="employee.php" class="nav-item <?= $currentPage == 'employee.php' ? 'active' : '' ?>"><img src="images/users.png" alt=""> Employee</a>
        <a href="departments.php" class="nav-item <?= $currentPage == 'departments.php' ? 'active' : '' ?>"><img src="images/building.png" alt=""> Departments</a>
        <a href="attendance.php" class="nav-item <?= $currentPage == 'attendance.php' ? 'active' : '' ?>"><img src="images/clock.png" alt=""> Attendance</a>
        <a href="leave_management.php" class="nav-item <?= $currentPage == 'leave_management.php' ? 'active' : '' ?>"><img src="images/calendar-month.png" alt=""> Leave Management</a>
        <a href="payroll.php" class="nav-item <?= $currentPage == 'payroll.php' ? 'active' : '' ?>"><img src="images/currency-peso.png" alt=""> Payroll</a>
        <a href="#" class="nav-item"><img src="images/user-plus.png" alt=""> Recruitment</a>
        <a href="#" class="nav-item"><img src="images/chart-line.png" alt=""> Performance</a>
    </nav>
    <div class="sidebar-section">
        <h3>Reports</h3>
        <a href="#" class="nav-item"><img src="images/report-analytics.png" alt=""> Reports</a>
    </div>
    <div class="sidebar-section">
        <h3>Settings</h3>
        <a href="#" class="nav-item"><img src="images/settings.png" alt=""> Settings</a>
    </div>
    <a href="login.php" class="nav-item logout"><img src="images/logout.png" alt=""> Logout</a>
</aside>