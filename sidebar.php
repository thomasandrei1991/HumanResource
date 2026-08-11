<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$userRole = $_SESSION['role'] ?? '';


// ==========================
// DASHBOARD LINK
// ==========================

$dashboardLink = 'dashboard.php';

if ($userRole === 'Employee') {

    $dashboardLink = 'employee_dashboard.php';

} elseif ($userRole === 'Department Head') {

    $dashboardLink = 'department_head_dashboard.php';

}

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


        <!-- ========================= -->
        <!-- DASHBOARD -->
        <!-- ========================= -->

        <a
            href="<?php echo htmlspecialchars($dashboardLink); ?>"
            class="nav-item <?php
                echo $currentPage === basename($dashboardLink)
                    ? 'active'
                    : '';
            ?>"
        >

            <img
                src="images/chart-bar-popular.png"
                alt="Dashboard"
            >

            <span>Dashboard</span>

        </a>


        <!-- ========================= -->
        <!-- ADMIN / HR -->
        <!-- ========================= -->

        <?php if ($userRole === 'Admin' || $userRole === 'HR'): ?>


            <!-- EMPLOYEES -->

            <a
                href="employee.php"
                class="nav-item <?php
                    echo $currentPage === 'employee.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <img
                    src="images/users.png"
                    alt="Employees"
                >

                <span>Employee</span>

            </a>


            <!-- DEPARTMENTS -->

            <a
                href="departments.php"
                class="nav-item <?php
                    echo $currentPage === 'departments.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <img
                    src="images/building.png"
                    alt="Departments"
                >

                <span>Departments</span>

            </a>


            <!-- DEPARTMENT HEADS -->

            <a
                href="department_heads.php"
                class="nav-item <?php
                    echo $currentPage === 'department_heads.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <img
                    src="images/building.png"
                    alt="Department Heads"
                >

                <span>Department Heads</span>

            </a>


        <?php endif; ?>


        <!-- ========================= -->
        <!-- ATTENDANCE -->
        <!-- ========================= -->

        <a
            href="attendance.php"
            class="nav-item <?php
                echo $currentPage === 'attendance.php'
                    ? 'active'
                    : '';
            ?>"
        >

            <img
                src="images/clock.png"
                alt="Attendance"
            >

            <span>Attendance</span>

        </a>


        <!-- ========================= -->
        <!-- LEAVE MANAGEMENT -->
        <!-- ========================= -->

        <a
            href="leave_management.php"
            class="nav-item <?php
                echo $currentPage === 'leave_management.php'
                    ? 'active'
                    : '';
            ?>"
        >

            <img
                src="images/calendar-month.png"
                alt="Leave Management"
            >

            <span>Leave Management</span>

        </a>


        <!-- ========================= -->
        <!-- PAYROLL -->
        <!-- ========================= -->

        <a
            href="payroll.php"
            class="nav-item <?php
                echo $currentPage === 'payroll.php'
                    ? 'active'
                    : '';
            ?>"
        >

            <img
                src="images/currency-peso.png"
                alt="Payroll"
            >

            <span>Payroll</span>

        </a>


        <!-- ========================= -->
        <!-- RECRUITMENT -->
        <!-- ========================= -->

        <?php if ($userRole === 'Admin' || $userRole === 'HR'): ?>

            <a
                href="recruitment.php"
                class="nav-item <?php
                    echo $currentPage === 'recruitment.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <img
                    src="images/user-plus.png"
                    alt="Recruitment"
                >

                <span>Recruitment</span>

            </a>

        <?php endif; ?>


        <!-- ========================= -->
        <!-- PERFORMANCE -->
        <!-- ========================= -->

        <a
            href="performance.php"
            class="nav-item <?php
                echo $currentPage === 'performance.php'
                    ? 'active'
                    : '';
            ?>"
        >

            <img
                src="images/chart-line.png"
                alt="Performance"
            >

            <span>Performance</span>

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
                class="nav-item <?php
                    echo $currentPage === 'reports.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <img
                    src="images/report-analytics.png"
                    alt="Reports"
                >

                <span>Reports</span>

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
                class="nav-item <?php
                    echo $currentPage === 'settings.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <img
                    src="images/settings.png"
                    alt="Settings"
                >

                <span>Settings</span>

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

        <img
            src="images/logout.png"
            alt="Logout"
        >

        <span>Logout</span>

    </a>

</aside>