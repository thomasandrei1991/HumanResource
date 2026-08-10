<?php

$currentPage = basename($_SERVER['PHP_SELF']);

$userRole = $_SESSION['role'] ?? '';

?>

<aside class="sidebar">

    <!-- ========================= -->
    <!-- BRAND -->
    <!-- ========================= -->

    <div class="brand">

        <div class="brand-icon">
            HR
        </div>

        <div>
            <h2>HR Portal</h2>
            <p>Human Resource</p>
        </div>

    </div>


    <!-- ========================= -->
    <!-- MAIN NAVIGATION -->
    <!-- ========================= -->

    <nav class="sidebar-nav">

        <!-- DASHBOARD -->

        <a
            href="dashboard.php"
            class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>"
        >
            <img src="images/chart-bar-popular.png" alt="">
            Dashboard
        </a>


        <!-- ADMIN / HR -->

        <?php if ($userRole === 'Admin' || $userRole === 'HR'): ?>

            <a
                href="employee.php"
                class="nav-item <?php echo $currentPage === 'employee.php' ? 'active' : ''; ?>"
            >
                <img src="images/users.png" alt="">
                Employee
            </a>


            <a
                href="departments.php"
                class="nav-item <?php echo $currentPage === 'departments.php' ? 'active' : ''; ?>"
            >
                <img src="images/building.png" alt="">
                Departments
            </a>

             <a href="department_heads.php"
                class="nav-item <?php echo $currentPage === 'department_heads.php' ? 'active' : ''; ?>"
             >
                <img src="images/building.png" alt="">
                Department Heads
            </a>

        <?php endif; ?>


        <!-- ATTENDANCE -->

        <a
            href="attendance.php"
            class="nav-item <?php echo $currentPage === 'attendance.php' ? 'active' : ''; ?>"
        >
            <img src="images/clock.png" alt="">
            Attendance
        </a>


        <!-- LEAVE MANAGEMENT -->

        <a
            href="leave_management.php"
            class="nav-item <?php echo $currentPage === 'leave_management.php' ? 'active' : ''; ?>"
        >
            <img src="images/calendar-month.png" alt="">
            Leave Management
        </a>


        <!-- PAYROLL -->

        <a
            href="payroll.php"
            class="nav-item <?php echo $currentPage === 'payroll.php' ? 'active' : ''; ?>"
        >
            <img src="images/currency-peso.png" alt="">
            Payroll
        </a>


        <!-- ADMIN / HR -->

        <?php if ($userRole === 'Admin' || $userRole === 'HR'): ?>

            <a
                href="recruitment.php"
                class="nav-item <?php echo $currentPage === 'recruitment.php' ? 'active' : ''; ?>"
            >
                <img src="images/user-plus.png" alt="">
                Recruitment
            </a>

        <?php endif; ?>


        <!-- PERFORMANCE -->

        <a
            href="performance.php"
            class="nav-item <?php echo $currentPage === 'performance.php' ? 'active' : ''; ?>"
        >
            <img src="images/chart-line.png" alt="">
            Performance
        </a>

    </nav>


    <!-- ========================= -->
    <!-- REPORTS -->
    <!-- ========================= -->

    <?php if ($userRole === 'Admin' || $userRole === 'HR'): ?>

        <div class="sidebar-section">

            <h3>Reports</h3>

            <a
                href="reports.php"
                class="nav-item <?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>"
            >
                <img src="images/report-analytics.png" alt="">
                Reports
            </a>

        </div>

    <?php endif; ?>


    <!-- ========================= -->
    <!-- SETTINGS -->
    <!-- ========================= -->

    <?php if ($userRole === 'Admin'): ?>

        <div class="sidebar-section">

            <h3>Settings</h3>

            <a
                href="settings.php"
                class="nav-item <?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>"
            >
                <img src="images/settings.png" alt="">
                Settings
            </a>

        </div>

    <?php endif; ?>


    <!-- ========================= -->
    <!-- LOGOUT -->
    <!-- ========================= -->

    <a
        href="logout.php"
        class="nav-item logout"
    >
        <img src="images/logout.png" alt="">
        Logout
    </a>

</aside>