<?php
    session_start();
    include "database.php";

    /*
    |--------------------------------------------------------------------------
    | LOGIN CHECK
    |--------------------------------------------------------------------------
    | Kick anyone who isn't logged in back to the login page before rendering
    | anything else on this page.
    */
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    /*
    |--------------------------------------------------------------------------
    | GET CURRENT USER
    |--------------------------------------------------------------------------
    */
    $userId   = intval($_SESSION['user_id']);          // cast to int for safe use in SQL below
    $userRole = $_SESSION['role'] ?? '';                 // default to '' if role isn't set

    /*
    |--------------------------------------------------------------------------
    | ROLE CHECK
    |--------------------------------------------------------------------------
    | Admin and HR : can see all attendance records, edit, and delete.
    | Employee     : can only see their own attendance; no edit or delete.
    */
    $isAdminOrHR = ($userRole === 'Admin' || $userRole === 'HR');

    /*
    |--------------------------------------------------------------------------
    | FORM MODE
    |--------------------------------------------------------------------------
    | Decide whether to show the add/edit attendance form instead of the
    | normal table + summary view. Only Admin/HR can trigger form mode.
    */


    $showForm = false;
    // "+ Record Attendance" button links here with ?show_form=1
    if (isset($_GET['show_form']) && $_GET['show_form'] === '1' && $isAdminOrHR) {
        $showForm = true;
    }
    // The "Edit" button on a table row links here with ?edit_id=123
    if (isset($_GET['edit_id']) && $isAdminOrHR) {
        $showForm = true;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/attendance.css">
    <title>Attendance | HR Dashboard</title>
</head>

<!--
    data-show-form: exposes to JS whether there's a pending attendance error,
    presumably so a script can auto-reopen the form panel after a failed submit.
    Note: this only checks 'attendance_error', not 'attendance_success', so a
    successful submit won't re-trigger this attribute (which is likely fine,
    since on success you'd want the form to close, not reopen).
-->

<body class="dashboard-page" data-show-form="<?php echo isset($_SESSION['attendance_error']) ? '1' : '0'; ?>">
<div class="dashboard-shell">
    <?php include 'sidebar.php'; ?>
    <main class="dashboard-main">
        <div class="dashboard-container">
            <div class="attendance-container">
                <div class="attendance-content">
                    <?php
                    
                    // Show a one-time success message, then clear it so it
                    // doesn't reappear on the next page load
                    if (isset($_SESSION['attendance_success'])) {
                        echo '<div class="success-message">' . htmlspecialchars($_SESSION['attendance_success']) . '</div>';
                        unset($_SESSION['attendance_success']);
                    }

                    // Same pattern for a one-time error message
                    if (isset($_SESSION['attendance_error'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_SESSION['attendance_error']) . '</div>';
                        unset($_SESSION['attendance_error']);
                    }
                    ?>

                    <!-- PAGE HEADER -->
                    <div class="page-header">
                        <div>
                            <p class="page-kicker">Time & Attendance</p>
                            <h1>Attendance</h1>
                        </div>
                        <?php if ($isAdminOrHR): ?>
                            <a
                                href="attendance.php?show_form=1"
                                class="primary-btn"
                                style="text-decoration: none; display: inline-block;"
                            >
                                + Record Attendance
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- FORM -->
                    <?php if ($showForm): ?>

                        <div class="attendance-form-wrapper">
                            <?php
                            $isEditMode = isset($_GET['edit_id']);

                            if ($isEditMode) {
                                /*
                                |--------------------------------------------------------------------------
                                | GET ATTENDANCE RECORD (edit mode)
                                |--------------------------------------------------------------------------
                                */
                                $editId = intval($_GET['edit_id']); // cast to int — prevents SQL injection here since it can only be numeric

                                $attendanceSql = "SELECT * FROM attendance WHERE id = $editId";
                                $attendanceResult = mysqli_query($conn, $attendanceSql);

                                if (!$attendanceResult || mysqli_num_rows($attendanceResult) === 0) {
                                    die("Attendance record not found.");
                                }

                                $attendance = mysqli_fetch_assoc($attendanceResult);

                                /*
                                |--------------------------------------------------------------------------
                                | GET EMPLOYEES (used by edit_attendance.php's dropdown)
                                |--------------------------------------------------------------------------
                                */
                                $employeesSql = "
                                    SELECT id, firstname, lastname
                                    FROM employees
                                    ORDER BY firstname ASC, lastname ASC
                                ";
                                $employees = mysqli_query($conn, $employeesSql);

                                if (!$employees) {
                                    die("Employee query failed: " . mysqli_error($conn));
                                }

                                // Hand off to the edit form partial, which has access to
                                // $attendance and $employees from this scope
                                include "edit_attendance.php";

                            } else {
                                // Add mode — no record to load, just show the blank form
                                include "add_attendance.php";
                            }
                            ?>
                        </div>

                    <?php else: ?>

                        <!-- SUMMARY CARDS -->
                        <!-- NOTE: these numbers (214, 12, 8, 14) are hardcoded, not pulled from the database -->
                        <div class="employee-summary">
                            <div class="summary-card blue">
                                <h3>Present Today</h3>
                                <p>214</p>
                            </div>
                            <div class="summary-card orange">
                                <h3>Late</h3>
                                <p>12</p>
                            </div>
                            <div class="summary-card red">
                                <h3>Absent</h3>
                                <p>8</p>
                            </div>
                            <div class="summary-card purple">
                                <h3>On Leave</h3>
                                <p>14</p>
                            </div>
                        </div>

                        <!-- ATTENDANCE TABLE -->
                        <div class="employee-panel">
                            <div class="panel-header">
                                <h2>Today's Attendance</h2>
                            </div>

                            <table class="dashboard-table employee-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Date</th>
                                        <th>Time In</th>
                                        <th>Time Out</th>
                                        <th>Status</th>
                                        <?php if ($isAdminOrHR): ?>
                                            <th>Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php
                                /*
                                |--------------------------------------------------------------------------
                                | ATTENDANCE QUERY
                                |--------------------------------------------------------------------------
                                | Admin / HR : show ALL attendance records, joined with employees for
                                |              name/department.
                                | Employee   : show ONLY the records belonging to the logged-in user,
                                |              matched via an extra join through the users table.
                                */
                                if ($isAdminOrHR) {
                                    $sql = "
                                        SELECT attendance.*, employees.firstname, employees.lastname, employees.department
                                        FROM attendance
                                        INNER JOIN employees ON attendance.employee_id = employees.id
                                        ORDER BY attendance.attendance_date DESC
                                    ";
                                } else {
                                    $sql = "
                                        SELECT attendance.*, employees.firstname, employees.lastname, employees.department
                                        FROM attendance
                                        INNER JOIN employees ON attendance.employee_id = employees.id
                                        INNER JOIN users ON users.employee_id = employees.id
                                        WHERE users.id = $userId
                                        ORDER BY attendance.attendance_date DESC
                                    ";
                                }

                                $result = mysqli_query($conn, $sql);

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {

                                        // Build initials for the little avatar circle, e.g. "John Smith" -> "JS"
                                        $initials = strtoupper(substr($row['firstname'], 0, 1) . substr($row['lastname'], 0, 1));

                                        /*
                                        |----------------------------------------------------------------
                                        | STATUS BADGE
                                        |----------------------------------------------------------------
                                        | Map the raw status string to a CSS class name for coloring
                                        | the badge (e.g. "On Leave" -> "on-leave").
                                        */
                                        switch ($row['status']) {
                                            case "Present":
                                                $badge = "present";
                                                break;
                                            case "Late":
                                                $badge = "late";
                                                break;
                                            case "Absent":
                                                $badge = "absent";
                                                break;
                                            case "On Leave":
                                                $badge = "on-leave";
                                                break;
                                            default:
                                                $badge = "pending";
                                        }
                                ?>
                                    <tr>
                                        <td>
                                            <div class="employee-name">
                                                <div class="emp-avatar blue-bg">
                                                    <?php echo htmlspecialchars($initials); ?>
                                                </div>
                                                <?php echo htmlspecialchars($row['firstname'] . " " . $row['lastname']); ?>
                                            </div>
                                        </td>

                                        <td><?php echo htmlspecialchars($row['department']); ?></td>

                                        <td>
                                            
                                            <?php
                                                echo date("F d, Y", strtotime($row['attendance_date']));
                                            ?>
                                        </td>

                                        <td><?php echo htmlspecialchars($row['time_in'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['time_out'] ?? ''); ?></td>

                                        <td>
                                            <span class="status-badge <?php echo $badge; ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>

                                        <?php if ($isAdminOrHR): ?>
                                        <td class="actions-cell">

                                            <!-- EDIT: a tiny GET form that just redirects to attendance.php?edit_id=X -->
                                            <form action="attendance.php" method="GET" style="display:inline;">
                                                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="edit-btn">✏️ Edit</button>
                                            </form>

                                            <!--
                                                DELETE: not a real form submit — data-* attributes are read by
                                                script.js, which presumably opens #deleteModal and fires an
                                                AJAX/fetch request to actually delete the record.
                                            -->
                                            <button
                                                type="button"
                                                class="employee-delete-btn"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-type="attendance"
                                                data-name="<?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>"
                                            >
                                                🗑️ Delete
                                            </button>

                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php
                                    } // end while
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="<?php echo $isAdminOrHR ? '7' : '6'; ?>" style="text-align:center;">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>
</div>

<!-- DELETE MODAL: shared confirmation modal, only rendered for Admin/HR since only they can delete -->
<?php if ($isAdminOrHR): ?>
<div id="deleteModal" class="modal hidden">
    <div class="modal-content">
        <h2>Delete Attendance</h2>
        <p id="deleteMessage">Are you sure you want to delete this attendance?</p>
        <div class="modal-actions">
            <button type="button" id="cancelDeleteBtn">Cancel</button>
            <button type="button" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="script.js"></script>
</body>
</html>