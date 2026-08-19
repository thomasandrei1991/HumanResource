<?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $currentPage = basename($_SERVER['PHP_SELF']);
    $userRole = $_SESSION['role'] ?? '';

    /* ==========================================================
    ROLE CHECKS
    ========================================================== */

    $isAdmin = ($userRole === 'Admin');
    $isHR = ($userRole === 'HR');
    $isEmployee = ($userRole === 'Employee');
    $isDepartmentHead = ($userRole === 'Department Head');
    $isAdminOrHR = ($isAdmin || $isHR);


    /* ==========================================================
    DASHBOARD LINK
    ========================================================== */

    $dashboardLink = 'dashboard.php';

    if ($isEmployee) {
        $dashboardLink = 'employee_dashboard.php';
    } elseif ($isDepartmentHead) {
        $dashboardLink = 'department_head_dashboard.php';
    }


    /* ==========================================================
    ACTIVE PAGE HELPER
    ========================================================== */

    $isActive = function ($page) use ($currentPage) {
        return $currentPage === basename($page) ? 'active' : '';
    };

?>

<aside class="sidebar">

    <!-- ======================================================
         BRAND
    ======================================================= -->

    <div class="brand">
        <div class="brand-icon">
            HR
        </div>
        <div class="brand-text">
            <h2>HR Portal</h2>
            <p>Human Resource</p>
        </div>
    </div>

    <!-- ======================================================
         MAIN NAVIGATION
    ======================================================= -->

    <nav class="sidebar-nav">

        <!-- ==================================================
             DASHBOARD
        =================================================== -->

        <a href="<?php echo htmlspecialchars($dashboardLink); ?>" class="nav-item <?php echo $isActive($dashboardLink); ?>">
            <img src="images/chart-bar-popular.png" alt="" aria-hidden="true">
            <span>Dashboard</span>
        </a>

        <!-- DEPARTMENT MANAGEMENT -->
        <?php if ($isDepartmentHead): ?>
            <a href="department_management.php" class="nav-item <?php echo $isActive('department_management.php'); ?>">
                <img src="images/building.png" alt="" aria-hidden="true">
                <span>Department Management</span>
            </a>
        <?php endif; ?>
        
        <!-- ADD SCHEDULE FORM -->
        <?php if ($isDepartmentHead): ?>
            <a href="schedule.php" class="nav-item <?php echo $isActive('schedule_management.php'); ?>">
                <img src="images/calendar-month.png" alt="" aria-hidden="true">
                <span>Schedule Management</span>
            </a>
        <?php endif; ?>

        <!-- ==================================================
             ADMIN / HR ONLY
        =================================================== -->

        <?php if ($isAdminOrHR): ?>

            <!-- EMPLOYEES -->
            <a href="employee.php" class="nav-item <?php echo $isActive('employee.php'); ?>">
                <img src="images/users.png" alt="" aria-hidden="true">
                <span>Employees</span>
            </a>


            <!-- DEPARTMENTS -->
            <a href="departments.php" class="nav-item <?php echo $isActive('departments.php'); ?>">
                <img src="images/building.png" alt="" aria-hidden="true">
                <span>Departments</span>
            </a>

            <!-- DEPARTMENT HEADS -->
            <a href="department_heads.php" class="nav-item <?php echo $isActive('department_heads.php'); ?>">
                <img src="images/building-plus.png" alt="" aria-hidden="true">
                <span>Department Heads</span>
            </a>

        <?php endif; ?>

        <!-- ==================================================
             EMPLOYEE ONLY
        =================================================== -->

        <?php if ($isEmployee): ?>

            <!-- MY ATTENDANCE -->
            <a href="my_history.php" class="nav-item <?php echo $isActive('my_history.php'); ?>">
                <img src="images/clock.png" alt="" aria-hidden="true">
                <span>My Attendance</span>
            </a>

            <!-- MY SCHEDULE -->
            <a href="my_schedule.php" class="nav-item <?php echo $isActive('my_schedule.php'); ?>">
                <img src="images/calendar-month.png" alt="" aria-hidden="true">
                <span>My Schedule</span>
            </a>

        <?php endif; ?>

        <!-- ==================================================
             LEAVE MANAGEMENT
        =================================================== -->

         <?php if ($isEmployee): ?>
            <a href="leave_management.php" class="nav-item <?php echo $isActive('leave_management.php'); ?>">
                <img src="images/calendar-month.png" alt="" aria-hidden="true">
                <span>Leave Management</span>
            </a>
         <?php endif; ?>

        <!-- ==================================================
             PAYROLL
        =================================================== -->
        <a href="payroll.php" class="nav-item <?php echo $isActive('payroll.php'); ?>">
            <img src="images/currency-peso.png" alt="" aria-hidden="true">
            <span>Payroll</span>
        </a>

        <!-- ==================================================
             RECRUITMENT
             ADMIN / HR ONLY
        =================================================== -->

        <?php if ($isAdminOrHR): ?>

            <a href="recruitment.php" class="nav-item <?php echo $isActive('recruitment.php'); ?>">
                <img src="images/user-plus.png" alt="" aria-hidden="true">
                <span>Recruitment</span>
            </a>

        <?php endif; ?>

        <!-- ==================================================
             PERFORMANCE
        =================================================== -->

        <a href="performance.php" class="nav-item <?php echo $isActive('performance.php'); ?>">
            <img src="images/chart-line.png" alt="" aria-hidden="true">
            <span>Performance</span>
        </a>

    </nav>


    <!-- ======================================================
         REPORTS
         ADMIN / HR ONLY
    ======================================================= -->

    <?php if ($isAdminOrHR): ?>

        <div class="sidebar-section">
            <a href="reports.php" class="nav-item <?php echo $isActive('reports.php'); ?>">
                <img src="images/report-analytics.png" alt="" aria-hidden="true">
                <span>Reports</span>
            </a>
        </div>

    <?php endif; ?>

    <!-- ======================================================
         SETTINGS
         ADMIN ONLY
    ======================================================= -->

    <?php if ($isAdmin): ?>

        <div class="sidebar-section">
            <a href="settings.php" class="nav-item <?php echo $isActive('settings.php'); ?>">
                <img src="images/settings.png" alt="" aria-hidden="true">
                <span>Settings</span>
            </a>
        </div>

    <?php endif; ?>

    <!-- ======================================================
         LOGOUT
    ======================================================= -->

    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item logout">
            <img src="images/logout.png" alt="" aria-hidden="true">
            <span>Logout</span>
        </a>
    </div>
</aside>