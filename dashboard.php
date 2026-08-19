<?php
    session_start();

    // Block access if the user isn't logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // ==========================
    // DATABASE CONNECTION
    // ==========================
    require_once 'database.php';

    // ==========================
    // USER INFORMATION
    // ==========================

    // Prefer full name, fall back to username, then a generic default
    $displayName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'User';
    $displayName = trim($displayName);

    // Split the name on whitespace so we can build initials from first/last
    $nameParts = preg_split('/\s+/', $displayName);

    $initials = '';

    if (!empty($nameParts)) {
        // First letter of the first name part
        $initials = strtoupper(substr($nameParts[0], 0, 1));

        // If there's more than one part (e.g. "Sarah Martinez"), add the
        // first letter of the LAST part too, so "Sarah Martinez" -> "SM"
        if (count($nameParts) > 1) {
            $initials .= strtoupper(substr(end($nameParts), 0, 1));
        }
    }

    // ==========================
    // DASHBOARD EMPLOYEE SUMMARY
    // ==========================
    // Three quick counts used in the summary cards further down the page

    $totalEmployees = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees")
    )['total'];

    $activeEmployees = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'Active'")
    )['total'];

    $onLeaveEmployees = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees WHERE employment_status = 'On Leave'")
    )['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <title>Dashboard | HR Dashboard</title>
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <?php include 'sidebar.php'; ?>

        <main class="dashboard-main">

            <!-- ==========================
                 DASHBOARD VIEW (default active panel)
                 ========================== -->
            <div class="view-panel active" id="dashboardView">
                <div class="dashboard-container">

                    <!-- Header: greeting + logged-in user's avatar/name -->
                    <div class="dashboard-header">
                        <div class="welcome">
                            <h1>Welcome back, <?php echo htmlspecialchars($displayName); ?>! 👋</h1>
                            <p>Here's what's happening with your team today.</p>
                        </div>
                        <div class="user-profile">
                            <div class="user-info">
                                <span><?php echo htmlspecialchars($displayName); ?></span>
                                <span>HR Manager</span>
                                <!-- NOTE: "HR Manager" is hardcoded here, not pulled from $_SESSION['role'] -->
                            </div>
                            <div class="avatar"><?php echo htmlspecialchars($initials); ?></div>
                        </div>
                    </div>

                    <!-- Summary cards: total / active / on-leave counts calculated above -->
                    <div class="employee-summary">
                        <div class="summary-card blue">
                            <h3>Total Staff</h3>
                            <p><?php echo $totalEmployees; ?></p>
                        </div>
                        <div class="summary-card green">
                            <h3>Active</h3>
                            <p><?php echo $activeEmployees; ?></p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p><?php echo $onLeaveEmployees; ?></p>
                        </div>
                    </div>

                    <div class="dashboard-content">

                        <!-- RECENT ATTENDANCE PANEL -->
                        <div class="dashboard-panel">
                            <h2>Recent Attendance</h2>
                            <div class="panel-header">
                                <a href="#" class="view-all">View All →</a>
                                <!-- NOTE: href="#" — this link doesn't actually go anywhere yet -->
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

                                <?php
                                // Pull the 5 most recent attendance records, joined with
                                // employees so we get name + department in one query
                                $attendanceQuery = mysqli_query($conn, "SELECT
                                        attendance.attendance_date,
                                        attendance.time_in,
                                        attendance.status,
                                        employees.firstname,
                                        employees.lastname,
                                        employees.department
                                    FROM attendance INNER JOIN employees ON attendance.employee_id = employees.id
                                    ORDER BY attendance.attendance_date DESC, attendance.time_in DESC LIMIT 5"
                                );

                                if ($attendanceQuery && mysqli_num_rows($attendanceQuery) > 0):
                                    while ($attendance = mysqli_fetch_assoc($attendanceQuery)):

                                        $firstname = $attendance['firstname'];
                                        $lastname  = $attendance['lastname'];
                                        $fullName  = $firstname . " " . $lastname;

                                        // Build initials for the avatar circle, e.g. "SM"
                                        $initials = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
                                        // NOTE: this reuses the $initials variable from the logged-in
                                        // user's initials above — it gets overwritten here on every loop,
                                        // so don't rely on $initials for the logged-in user after this point

                                        // Attendance status → CSS class, same mapping used on attendance.php
                                        switch ($attendance['status']) {
                                            case 'Present':
                                                $statusClass = 'present';
                                                break;
                                            case 'Late':
                                                $statusClass = 'late';
                                                break;
                                            case 'Absent':
                                                $statusClass = 'absent';
                                                break;
                                            case 'On Leave':
                                                $statusClass = 'on-leave';
                                                break;
                                            default:
                                                $statusClass = 'pending';
                                        }
                                ?>
                                    <tr>
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar blue-bg">
                                                    <?php echo htmlspecialchars($initials); ?>
                                                </div>
                                                <?php echo htmlspecialchars($fullName); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($attendance['department']); ?></td>
                                        <td><?php echo htmlspecialchars($attendance['time_in'] ?? '--'); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($attendance['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center;">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                </tbody>
                            </table>
                        </div>

                        <!--
                            RECENT ACTIVITY PANEL
                            NOTE: this whole list is static/hardcoded HTML — "Sarah Martinez",
                            "Rachel Kim", timestamps like "5 min ago" etc. are not coming from
                            the database. This would need its own activity-log table and query
                            to become dynamic.
                        -->
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

            <!-- ==========================
                 EMPLOYEE VIEW (hidden panel — likely toggled via sidebar/JS)
                 ========================== -->
            <div class="view-panel" id="employeeView">
                <div class="form-container dashboard-container">
                    <div class="employee-container">

                        <div class="page-header">
                            <div>
                                <p class="page-kicker">People Management</p>
                                <h1>Employees</h1>
                            </div>
                            <button class="primary-btn">+ Add Employee</button>
                            <!-- NOTE: plain <button>, no onclick/form action — needs JS (or a link)
                                 to actually open the add-employee form you built earlier -->
                        </div>

                        <!-- Same summary numbers reused from the dashboard view above -->
                        <div class="summary-card blue">
                            <h3>Total Staff</h3>
                            <p><?php echo $totalEmployees; ?></p>
                        </div>
                        <div class="summary-card green">
                            <h3>Active</h3>
                            <p><?php echo $activeEmployees; ?></p>
                        </div>
                        <div class="summary-card purple">
                            <h3>On Leave</h3>
                            <p><?php echo $onLeaveEmployees; ?></p>
                        </div>

                        <!--
                            EMPLOYEE DIRECTORY TABLE
                            NOTE: this entire table is static/hardcoded — Sarah Martinez, Alex Johnson,
                            Rachel Kim, Marcus Thompson are placeholder rows, not a real query against
                            the employees table. This needs to be replaced with a mysqli_query loop
                            similar to the attendance table above.
                        -->
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
                                    <?php
                                    $employeeDirectoryQuery = mysqli_query(
                                        $conn,
                                        "SELECT firstname, lastname, department, position, employment_status
                                        FROM employees
                                        ORDER BY firstname ASC, lastname ASC"
                                    );

                                    if (
                                        $employeeDirectoryQuery &&
                                        mysqli_num_rows($employeeDirectoryQuery) > 0
                                    ):

                                        while (
                                            $employee = mysqli_fetch_assoc($employeeDirectoryQuery)
                                        ):

                                            $firstname = $employee['firstname'];
                                            $lastname = $employee['lastname'];
                                            $fullName = $firstname . " " . $lastname;

                                            // Generate initials
                                            $initials = strtoupper(
                                                substr($firstname, 0, 1) .
                                                substr($lastname, 0, 1)
                                            );

                                            // Employment status → CSS class
                                            switch ($employee['employment_status']) {

                                                case 'Active':
                                                    $statusClass = 'present';
                                                    break;

                                                case 'On Leave':
                                                    $statusClass = 'on-leave';
                                                    break;

                                                case 'Inactive':
                                                    $statusClass = 'absent';
                                                    break;

                                                default:
                                                    $statusClass = 'pending';
                                            }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar blue-bg">
                                                    <?php echo htmlspecialchars($initials); ?>
                                                </div>
                                                <?php echo htmlspecialchars($fullName); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($employee['department']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($employee['position']); ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($employee['employment_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center;">
                                            No employees found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
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