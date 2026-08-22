<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $currentPage = basename($_SERVER['PHP_SELF']);
    // Gawing lowercase para safe sa anumang casing sa database
    $userRole    = strtolower(trim($_SESSION['role'] ?? ''));

    /* ==========================================================
       1. ROLE CHECKS (Case-Insensitive)
       ========================================================== */
    $isAdmin          = ($userRole === 'admin');
    $isHR             = ($userRole === 'hr');
    $isEmployee       = ($userRole === 'employee');
    $isDepartmentHead = ($userRole === 'department head' || $userRole === 'dept head');
    $isAdminOrHR      = ($isAdmin || $isHR);

    /* ==========================================================
       2. DASHBOARD & PROFILE LINK ROUTING
       ========================================================== */
    $dashboardLink = 'dashboard.php';
    $profileLink   = 'employee_profile.php'; // Default

    if ($isEmployee) {
        $dashboardLink = 'employee_dashboard.php';
        $profileLink   = 'employee_profile.php';
    } elseif ($isDepartmentHead) {
        $dashboardLink = 'department_head_dashboard.php';
        $profileLink   = 'department_head_profile.php'; // Mapupunta na rito si Dept Head
    }

    /* ==========================================================
       3. ACTIVE PAGE HELPER FUNCTION
       ========================================================== */
    $isActive = function ($page) use ($currentPage) {
        return $currentPage === basename($page) ? 'active' : '';
    };
?>

<aside class="sidebar">

    <!-- ======================================================
         BRAND / HEADER
    ======================================================= -->
    <div class="brand">
        <div class="brand-icon">HR</div>
        <div class="brand-text">
            <h2>HR Portal</h2>
            <p>Human Resource</p>
        </div>
    </div>

    <!-- ======================================================
         MAIN NAVIGATION
    ======================================================= -->
    <nav class="sidebar-nav">

        <!-- [MENU 1] DASHBOARD (Lahat ng roles) -->
        <a href="<?php echo htmlspecialchars($dashboardLink); ?>" class="nav-item <?php echo $isActive($dashboardLink); ?>">
            <img src="images/chart-bar-popular.png" alt="" aria-hidden="true">
            <span>Dashboard</span>
        </a>

        <!-- [MENU 2] MY PROFILE (Para sa Employee at Department Head) -->
        <?php if ($isDepartmentHead || $isEmployee): ?>
            <a href="<?php echo htmlspecialchars($profileLink); ?>" class="nav-item <?php echo $isActive($profileLink); ?>">
                <img src="images/users.png" alt="" aria-hidden="true">
                <span>My Profile</span>
            </a>
        <?php endif; ?>

        <!-- ==================================================
             DEPARTMENT HEAD SPECIFIC MENUS
        =================================================== -->
        <?php if ($isDepartmentHead): ?>
            <!-- DEPARTMENT MANAGEMENT -->
            <a href="department_management.php" class="nav-item <?php echo $isActive('department_management.php'); ?>">
                <img src="images/building.png" alt="" aria-hidden="true">
                <span>Department Management</span>
            </a>

            <!-- SCHEDULE MANAGEMENT -->
            <a href="schedule.php" class="nav-item <?php echo $isActive('schedule.php') || $isActive('schedule_management.php'); ?>">
                <img src="images/calendar-month.png" alt="" aria-hidden="true">
                <span>Schedule Management</span>
            </a>
        <?php endif; ?>

        <!-- ==================================================
             ADMIN / HR SPECIFIC MENUS
        =================================================== -->
        <?php if ($isAdminOrHR): ?>
            <!-- EMPLOYEES DIRECTORY -->
            <a href="employee.php" class="nav-item <?php echo $isActive('employee.php'); ?>">
                <img src="images/users.png" alt="" aria-hidden="true">
                <span>Employees</span>
            </a>

            <!-- DEPARTMENTS OVERVIEW -->
            <a href="departments.php" class="nav-item <?php echo $isActive('departments.php'); ?>">
                <img src="images/building.png" alt="" aria-hidden="true">
                <span>Departments</span>
            </a>

            <!-- DEPARTMENT HEADS MANAGEMENT -->
            <a href="department_heads.php" class="nav-item <?php echo $isActive('department_heads.php'); ?>">
                <img src="images/building-plus.png" alt="" aria-hidden="true">
                <span>Department Heads</span>
            </a>
        <?php endif; ?>

        <!-- ==================================================
             EMPLOYEE SPECIFIC MENUS
        =================================================== -->
        <?php if ($isEmployee): ?>
            <!-- MY ATTENDANCE HISTORY -->
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
             SHARED / GENERAL MENUS
        =================================================== -->

        <!-- LEAVE MANAGEMENT (Employee & Dept Head) -->
        <?php if ($isEmployee || $isDepartmentHead): ?>
            <a href="leave_management.php" class="nav-item <?php echo $isActive('leave_management.php'); ?>">
                <img src="images/calendar-month.png" alt="" aria-hidden="true">
                <span>Leave Management</span>
            </a>
        <?php endif; ?>

        <!-- PAYROLL -->
        <a href="payroll.php" class="nav-item <?php echo $isActive('payroll.php'); ?>">
            <img src="images/currency-peso.png" alt="" aria-hidden="true">
            <span>Payroll</span>
        </a>

        <!-- RECRUITMENT (Admin & HR) -->
        <?php if ($isAdminOrHR): ?>
            <a href="recruitment.php" class="nav-item <?php echo $isActive('recruitment.php'); ?>">
                <img src="images/user-plus.png" alt="" aria-hidden="true">
                <span>Recruitment</span>
            </a>
        <?php endif; ?>

        <!-- PERFORMANCE -->
        <a href="performance.php" class="nav-item <?php echo $isActive('performance.php'); ?>">
            <img src="images/chart-line.png" alt="" aria-hidden="true">
            <span>Performance</span>
        </a>

    </nav>

    <!-- ======================================================
         BOTTOM SECTIONS (REPORTS & SETTINGS)
    ======================================================= -->

    <!-- REPORTS (Admin & HR) -->
    <?php if ($isAdminOrHR): ?>
        <div class="sidebar-section">
            <a href="reports.php" class="nav-item <?php echo $isActive('reports.php'); ?>">
                <img src="images/report-analytics.png" alt="" aria-hidden="true">
                <span>Reports</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- SYSTEM SETTINGS (Admin Only) -->
    <?php if ($isAdmin): ?>
        <div class="sidebar-section">
            <a href="settings.php" class="nav-item <?php echo $isActive('settings.php'); ?>">
                <img src="images/settings.png" alt="" aria-hidden="true">
                <span>Settings</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- ======================================================
         LOGOUT FOOTER
    ======================================================= -->
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item logout">
            <img src="images/logout.png" alt="" aria-hidden="true">
            <span>Logout</span>
        </a>
    </div>

</aside>